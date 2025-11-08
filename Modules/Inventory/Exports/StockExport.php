<?php

namespace Modules\Inventory\Exports;

use Modules\Inventory\Entities\InventoryItem;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class StockExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $search;
    protected $category;
    protected $stockStatus;

    public function __construct($search = null, $category = null, $stockStatus = null)
    {
        $this->search = $search;
        $this->category = $category;
        $this->stockStatus = $stockStatus;
    }

    public function headings(): array
    {
        return [
            __('inventory::modules.inventoryItem.name'),
            __('inventory::modules.inventoryItem.category'),
            __('inventory::modules.stock.currentStock'),
            __('inventory::modules.inventoryItem.unit'),
            __('inventory::modules.stock.minStock'),
            __('inventory::modules.stock.stockStatus'),
            __('inventory::modules.stock.unitPurchasePrice'),
            __('inventory::modules.stock.cost'),
        ];
    }

    public function map($item): array
    {
        $stockStatus = $item->getStockStatus();
        
        return [
            $item->name,
            $item->category->name ?? '-',
            number_format($item->current_stock, 2),
            $item->unit->symbol ?? '-',
            number_format($item->threshold_quantity, 2),
            $stockStatus['status'],
            currency_format($item->unit_purchase_price, restaurant()->currency_id),
            currency_format($item->unit_purchase_price * $item->current_stock, restaurant()->currency_id),
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return $defaultStyle
            ->getFont()
            ->setName('Arial');
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'name' => 'Arial'], 'fill'  => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => array('rgb' => 'f5f5f5'),
            ]],
        ];
    }

    public function collection()
    {
        // Set MySQL to non-strict mode for this query
        DB::statement("SET SESSION sql_mode=''");

        $query = InventoryItem::with(['category', 'unit', 'stocks'])
            ->where('inventory_items.branch_id', branch()->id)
            ->select('inventory_items.*')
            ->selectRaw('COALESCE(SUM(inventory_stocks.quantity), 0) as current_stock')
            ->leftJoin('inventory_stocks', function($join) {
                $join->on('inventory_items.id', '=', 'inventory_stocks.inventory_item_id')
                    ->where('inventory_stocks.branch_id', '=', branch()->id);
            })
            ->groupBy('inventory_items.id');

        // Apply search filter
        if ($this->search) {
            $query->where('inventory_items.name', 'like', '%' . $this->search . '%');
        }

        // Apply category filter
        if ($this->category) {
            $query->where('inventory_items.inventory_item_category_id', $this->category);
        }

        // Apply stock status filter
        if ($this->stockStatus) {
            switch ($this->stockStatus) {
                case 'in_stock':
                    $query->havingRaw('current_stock > inventory_items.threshold_quantity');
                    break;
                case 'low_stock':
                    $query->havingRaw('current_stock > 0 AND current_stock <= inventory_items.threshold_quantity');
                    break;
                case 'out_of_stock':
                    $query->havingRaw('current_stock <= 0');
                    break;
            }
        }

        $result = $query->get();

        // Reset SQL mode back to default after query execution
        DB::statement("SET SESSION sql_mode=(SELECT @@global.sql_mode)");

        return $result;
    }
}

