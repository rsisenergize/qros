<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\MenuItem;
use App\Scopes\AvailableMenuItemScope;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Helper\Common;

class ItemReportExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected string $startDateTime, $endDateTime;
    protected string $startTime, $endTime, $timezone, $searchTerm;
    protected $headingDateTime, $headingEndDateTime, $headingStartTime, $headingEndTime;

    public function __construct(string $startDateTime, string $endDateTime, string $startTime, string $endTime, string $timezone, ?string $searchTerm = '')
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->timezone = $timezone;
        $this->searchTerm = $searchTerm ?? '';

        $this->headingDateTime = Carbon::parse($startDateTime)->setTimezone($timezone)->format('Y-m-d');
        $this->headingEndDateTime = Carbon::parse($endDateTime)->setTimezone($timezone)->format('Y-m-d');
        $this->headingStartTime = Carbon::parse($startTime)->setTimezone($timezone)->format('h:i A');
        $this->headingEndTime = Carbon::parse($endTime)->setTimezone($timezone)->format('h:i A');
    }

    public function headings(): array
    {
        $headingTitle = $this->headingDateTime === $this->headingEndDateTime
            ? __('modules.report.salesDataFor') . " {$this->headingDateTime}, " . __('modules.report.timePeriod') . " {$this->headingStartTime} - {$this->headingEndTime}"
            : __('modules.report.salesDataFrom') . " {$this->headingDateTime} " . __('app.to') . " {$this->headingEndDateTime}, " . __('modules.report.timePeriodEachDay') . " {$this->headingStartTime} - {$this->headingEndTime}";

        return [
            [__('menu.itemReport') . ' ' . $headingTitle],
            [
                __('modules.menu.itemName'),
                __('modules.menu.categoryName'),
                __('modules.report.quantitySold'),
                __('modules.report.sellingPrice'),
                __('modules.report.totalRevenue'),
            ]
        ];
    }
    public function map($item): array
    {
        $rows = [];

        // Check if the item has variations
        if ($item->variations->count() > 0) {
            foreach ($item->variations as $variation) {
                $quantitySold = $item->orders->where('menu_item_variation_id', $variation->id)->sum('quantity');
                $rows[] = [
                    $item->item_name . ' (' . $variation->variation . ')',
                    $item->category->category_name,
                    $quantitySold,
                    currency_format($variation->price, restaurant()->currency_id),
                    currency_format($variation->price * $quantitySold, restaurant()->currency_id),
                ];
            }
        } else {
            // If there are no variations, just use the item name and price
            $quantitySold = $item->orders->sum('quantity');
            $rows[] = [
                $item->item_name,
                $item->category->category_name,
                $quantitySold,
                currency_format($item->price, restaurant()->currency_id),
                currency_format($item->price * $quantitySold, restaurant()->currency_id),
            ];
        }

        return $rows;
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

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return MenuItem::withoutGlobalScope(AvailableMenuItemScope::class)->with(['orders' => function ($q) {
            return $q->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereBetween('orders.date_time', [$this->startDateTime, $this->endDateTime])
                ->where('orders.status', 'paid')
                ->where(function ($q) {
                    if ($this->startTime < $this->endTime) {
                        $q->whereRaw("TIME(orders.date_time) BETWEEN ? AND ?", [$this->startTime, $this->endTime]);
                    } else {
                        $q->where(function ($sub) {
                            $sub->whereRaw("TIME(orders.date_time) >= ?", [$this->startTime])
                                ->orWhereRaw("TIME(orders.date_time) <= ?", [$this->endTime]);
                        });
                    }
                });
        }, 'category', 'variations'])
            ->where(function ($query) {
                if ($this->searchTerm) {
                    $query->where(function ($q) {
                        $safeTerm = Common::safeString($this->searchTerm);

                        $q->where('item_name', 'like', '%' . $safeTerm . '%')
                            ->orWhereHas('category', function ($q) use ($safeTerm) {
                                $q->where('category_name', 'like', '%' . $safeTerm . '%');
                            });
                    });
                }
            })->get();
    }
}
