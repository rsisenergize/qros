<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Inventory\Entities\PurchaseOrder;
use Modules\Inventory\Entities\Supplier;
use Modules\Inventory\Entities\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class PurchaseOrderController extends Controller
{
    /**
     * Display the purchase orders page.
     */
    public function index()
    {
        abort_if(!in_array('Inventory', restaurant_modules()), 403);
        abort_if(!user_can('Show Purchase Order'), 403);
        
        return view('inventory::purchase-orders.index');
    }

    public function create()
    {
        $suppliers = Supplier::where('restaurant_id', auth()->user()->restaurant_id)->get();
        $inventoryItems = InventoryItem::where('branch_id', auth()->user()->branch_id)
            ->with('unit')
            ->get();

        return view('inventory::purchase-orders.create', compact('suppliers', 'inventoryItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $po = PurchaseOrder::create([
                'branch_id' => auth()->user()->branch_id,
                'supplier_id' => $validated['supplier_id'],
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
                'status' => 'draft',
            ]);

            $po->generatePoNumber();
            $po->save();

            foreach ($validated['items'] as $item) {
                $po->items()->create([
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            $po->update(['total_amount' => $po->items->sum('subtotal')]);
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order created successfully.');
    }

    /**
     * Generate PDF for the purchase order
     */
    public function generatePdf(PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->branch_id !== auth()->user()->branch_id, 403);

        $pdf = PDF::loadView('inventory::purchase-orders.pdf', [
            'purchaseOrder' => $purchaseOrder->load(['supplier', 'items.inventoryItem.unit', 'createdBy', 'branch.restaurant'])
        ]);

        return $pdf->download("PO-{$purchaseOrder->po_number}.pdf");
    }

    // ... Add other controller methods (show, edit, update, destroy) ...
} 