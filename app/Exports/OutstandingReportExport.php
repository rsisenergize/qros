<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Expenses;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OutstandingReportExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::createFromFormat('m/d/Y', $startDate)->toDateString();
        $this->endDate = Carbon::createFromFormat('m/d/Y', $endDate)->toDateString();
    }

    public function headings(): array
    {
        return [
            [__('modules.expenses.reports.outstandingPaymentReport') . ' ' . $this->startDate .' - ' . $this->endDate],
            [
                __('modules.expenses.totalPaymentDue'),
                __('modules.expenses.lastDueDate'),
                __('modules.expenses.paymentStatus'),
            ]
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->total_amount, // Summed amount
            $expense->last_due_date, // Latest due date for pending payments
            'Pending'
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return $defaultStyle->getFont()->setName('Arial');
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'name' => 'Arial'], 'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'f5f5f5'],
            ]],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Expenses::where('payment_status', '=', 'pending')
            ->whereBetween('payment_due_date', [$this->startDate, $this->endDate])
            ->selectRaw('SUM(expenses.amount) as total_amount, MAX(expenses.expense_date) as last_due_date')
            ->groupBy('expenses.payment_status')
            ->orderByDesc('total_amount')
            ->get();
    }

}
