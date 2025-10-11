<?php

namespace App\Exports;

use App\Models\ItemCategory;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoryReportExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected string $startDateTime, $endDateTime, $startTime, $endTime, $timezone, $offset;
    protected $headingDateTime, $headingEndDateTime, $headingStartTime, $headingEndTime;

    public function __construct(string $startDateTime, string $endDateTime, string $startTime, string $endTime, string $timezone)
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->timezone = $timezone;

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
            [__('menu.categoryReport') . ' ' . $headingTitle],
            [
            __('modules.menu.itemCategory'),
            __('modules.report.quantitySold'),
            __('modules.order.amount'),
            ]
        ];
    }

    public function map($item): array
    {
        return [
            $item->category_name,
            $item->orders->sum('quantity') ?: 0,
            currency_format($item->orders->sum(function($order) { return $order->quantity * $order->price; }), restaurant()->currency_id)
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

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ItemCategory::with(['orders' => function ($q) {
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
        }])->get();
    }

}
