<?php

namespace App\Livewire\Order;

use Log;
use App\Models\Order;
use Livewire\Component;
use App\Models\Printer as PrinterSettings;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Traits\PrinterSetting;

class DirectOrderPrint extends Component
{
    use LivewireAlert, PrinterSetting;

    public $order;
    protected $connector;
    protected $printer;
    protected $printerTypeLines = 48;
    protected $charPerLine;
    public $loading = false;

    public function mount()
    {
        $this->charPerLine = $this->printerTypeLines;
    }

    public function printKotThermal()
    {
        try {
            $this->handleOrderPrint($this->order->id);
            $this->alert('success', __('modules.kot.print_success'));
        } catch (\Exception $e) {
            \Log::error('KOT Print Error: ' . $e->getMessage());
            $this->alert('error', __('modules.kot.print_failed', ['error' => $e->getMessage()]));
        }
    }

    public function render()
    {
        return view('livewire.order.direct-order-print');
    }
}
