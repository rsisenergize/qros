<?php

namespace App\Traits;

use Exception;
use App\Models\Kot;
use App\Models\Order;
use App\Models\KotPlace;
use App\Models\MultipleOrder;
use Illuminate\Support\Facades\Log;
use App\Models\Printer as PrinterSettings;
use App\Models\PrintJob;
use App\Events\PrintJobCreated;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\KotController;
use Barryvdh\DomPDF\Facade\Pdf;

trait PrinterSetting
{
    protected $printer;
    protected $charPerLine;
    protected $indentSize;
    protected $htmlContent = null;
    protected $imagePath = null;
    protected $imageFilename = null;
    protected $printerSetting;


    public function handleKotPrint($kotId, $kotPlaceId = null, $alsoPrintOrder = false)
    {
        $kotPlace = KotPlace::findOrFail($kotPlaceId);
        $printerSetting = $this->getActivePrinter($kotPlace->printer_id);
        $this->printerSetting = $printerSetting;


        $kotPlaceId = $kotPlaceId ?? 1;
        $width = $this->getPrintWidth(); // 80mm for fullWidth approach
        $thermal = true;
        // Generate the KOT content using KotController to avoid duplication
        $content = (new KotController())->printKot($kotId, $kotPlaceId, $width, $thermal)->render();

        if ($this->checkGeneratePdf()) {
            $this->generateKotPdf($kotId, $content);
        } else {
            $this->generateKotImage($kotId, $kotPlaceId, $content);
        }


        // Then proceed with the original print logic
        $this->executeKotPrint($kotId, $kotPlaceId, $alsoPrintOrder);
    }

    /**
     * Generate KOT image using html-to-image JavaScript approach
     */
    public function generateKotImage($kotId, $kotPlaceId = null, $content)
    {
        //  Log::info("generateKotImage called for KOT ID: {$kotId}, Place ID: {$kotPlaceId}");

        try {
            // Add a small delay to prevent race conditions when multiple KOTs are printed simultaneously
            usleep(200000); // 200ms delay

            // Use html-to-image approach by dispatching a JavaScript event
            // This will trigger the image capture in the frontend
            $this->dispatch('saveKotImageFromPrint', $kotId, $kotPlaceId, $content);

            // Log success
            // Log::info("KOT image save event dispatched for KOT ID: {$kotId}");
        } catch (\Exception $e) {
            // Log::error("Failed to dispatch KOT image save event: " . $e->getMessage());
            // Log::error("Stack trace: " . $e->getTraceAsString());
            // Don't throw exception to avoid breaking the print process
        }
    }

    private function generateKotPdf($kotId, $content)
    {
        $width = $this->getPrintWidth();
        $paperWidthInPoints = $width * 2.85;
        $paperHeightInPoints = 800;
        $pdf = Pdf::loadHTML($content)
            ->setPaper([0, 0, $paperWidthInPoints, $paperHeightInPoints], 'portrait');
        $pdf->save(public_path('user-uploads/print/kot-' . $kotId . '.pdf'));
    }

    /**
     * Execute the original KOT print logic
     */
    private function executeKotPrint($kotId, $kotPlaceId = null, $alsoPrintOrder = false)
    {
        $kotPlace = KotPlace::findOrFail($kotPlaceId);
        $printerSetting = $this->getActivePrinter($kotPlace->printer_id);
        $this->printerSetting = $printerSetting;

        if (!$printerSetting) {
            throw new \Exception(__('messages.noActiveKotPrinterConfigured'));
        }


        $kot = Kot::with('items', 'order.waiter', 'table')->find($kotId);

        if ($this->checkGeneratePdf()) {
            $this->imageFilename = 'kot-' . $kotId . '.pdf';
        } else {
            $this->imageFilename = 'kot-' . $kotId . '.png';
        }

        $this->createPrintJobRecord($kot->branch_id, $kot->branch->restaurant_id);

        if ($alsoPrintOrder) {
            $kot = Kot::findOrFail($kotId);
            $this->handleOrderPrint($kot->order_id);
        }
    }

    private function loadKotWithRelations($kotId)
    {
        return Kot::with([
            'items.menuItem',
            'items.menuItemVariation',
            'items.modifierOptions',
            'order.table',
            'order.customer',
            'order.waiter',
            'order.items.menuItem',
            'order.items.menuItemVariation',
            'order.items.modifierOptions',
            'order.charges.charge',
            'order.taxes.tax',
            'order.payments'
        ])->findOrFail($kotId);
    }




    public function handleOrderPrint($orderId)
    {
        Log::info("handleOrderPrint called for Order ID: {$orderId}");

        // Load the order to verify what we're actually printing
        $order = Order::find($orderId);
        if ($order) {
            Log::info("Order details - ID: {$order->id}, Order Number: {$order->order_number}, Created: {$order->created_at}");
        } else {
            Log::error("Order with ID {$orderId} not found!");
        }

        $orderPlace = MultipleOrder::first();
        $printerSetting = $this->getActivePrinter($orderPlace->printer_id);

        $this->printerSetting = $printerSetting;


        $width = $this->getPrintWidth(); // 80mm for fullWidth approach
        $thermal = true;

        // Generate the Order content using OrderController to avoid duplication
        $content = (new OrderController())->printOrder($orderId, $width, $thermal)->render();

        if ($this->checkGeneratePdf()) {
            $this->generateOrderPdf($orderId, $content);
        } else {
            $this->generateOrderImage($orderId, $content);
        }


        // Then proceed with the original print logic
        $this->executeOrderPrint($orderId);
    }

    /**
     * Generate Order image using html-to-image JavaScript approach
     */
    private function generateOrderImage($orderId, $content)
    {
        Log::info("generateOrderImage called for Order ID: {$orderId}");

        try {
            // Add a delay to prevent conflicts with KOT image generation
            usleep(500000); // 500ms delay

            // Use html-to-image approach by dispatching a JavaScript event
            // This will trigger the image capture in the frontend
            $this->dispatch('saveOrderImageFromPrint', $orderId, $content);

            // Log success
            Log::info("Order image save event dispatched for Order ID: {$orderId}");
        } catch (\Exception $e) {
            Log::error("Failed to dispatch Order image save event: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            // Don't throw exception to avoid breaking the print process
        }
    }

    private function generateOrderPdf($orderId, $content)
    {
        $width = $this->getPrintWidth();
        // Calculate paper width in points (1mm = 2.83465 points)
        // Common thermal printer widths: 58mm, 80mm
        $paperWidthInPoints = $width * 2.83;
        $paperHeightInPoints = 800; // Dynamic height, will be adjusted by content

        $pdf = Pdf::loadHTML($content)
            ->setPaper([0, 0, $paperWidthInPoints, $paperHeightInPoints], 'portrait');
        $pdf->save(public_path('user-uploads/print/order-' . $orderId . '.pdf'));
    }

    /**
     * Execute the original Order print logic
     */
    private function executeOrderPrint($orderId)
    {
        $orderPlace = MultipleOrder::first();
        $printerSetting = $this->getActivePrinter($orderPlace->printer_id);

        $this->printerSetting = $printerSetting;


        if (!$printerSetting) {
            throw new \Exception('No active order printer configured.');
        }


        $this->printOrderThermal($orderId);
    }

    public function printOrderThermal($orderId)
    {
        if ($this->checkGeneratePdf()) {
            $this->imageFilename = 'order-' . $orderId . '.pdf';
        } else {
            $this->imageFilename = 'order-' . $orderId . '.png';
        }

        $order = $this->loadOrderWithRelations($orderId);

        $this->createPrintJobRecord($order->branch_id, $order->branch->restaurant_id);
        $this->alert('success', __('modules.kot.print_success'));
    }

    private function loadOrderWithRelations($orderId)
    {
        return Order::with([
            'table',
            'customer',
            'waiter',
            'items.menuItem',
            'items.menuItemVariation',
            'items.modifierOptions',
            'charges.charge',
            'taxes.tax',
            'payments'
        ])->findOrFail($orderId);
    }

    private function getActivePrinter($printerId)
    {
        return PrinterSettings::where('is_active', 1)
            ->where('id', $printerId)
            ->first();
    }


    private function createPrintJobRecord($branchId = null, $restaurantId = null)
    {
        $printerSetting = $this->printerSetting;

        $printJob = PrintJob::create([
            'image_filename' => $this->imageFilename,
            'restaurant_id' => $restaurantId,
            'branch_id' => $branchId,
            'status' => 'pending',
            'printer_id' => $printerSetting->id ?? null,
        ]);

        // Dispatch event for print job creation
        event(new PrintJobCreated($printJob));


        return $printJob;
    }


    private function getPrintWidth()
    {

        return match ($this->printerSetting->print_format ?? 'thermal80mm') {
            'thermal56mm' => 56,
            'thermal112mm' => 112,
            default => 80,
        };
    }


    public function ifMobileDevice()
    {
        $isMobile = false;

        if (request()->header('User-Agent')) {
            $agent = strtolower(request()->header('User-Agent'));
            $isMobile = preg_match('/mobile|android|iphone|ipad|phone/i', $agent);
        }

        return $isMobile ?? false;
    }

    public function ifDesktopDevice()
    {
        return !$this->ifMobileDevice();
    }

    private function checkGeneratePdf()
    {
        $currencySymbol = restaurant()?->currency?->currency_symbol ?? '';

        return ($this->printerSetting->print_type == 'pdf' || $this->ifMobileDevice());
    }

    private function hasUnicode($string)
    {
        return mb_check_encoding($string, 'UTF-8') && preg_match('/[^\x00-\x7F]/', $string);
    }
}
