<?php

namespace Modules\Inventory\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Modules\Inventory\Entities\InventoryMovement;

#[Layout('layouts.app')]
class CostAnalysisReport extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $searchTerm = '';
    public $chartOptions = [];

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->loadReportData();
    }

    public function loadReportData()
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
        
        // Get COGS data grouped by date
        $cogsData = $this->getCOGSData($startDate, $endDate);

        $this->chartOptions = [
            'chart' => [
                'height' => 420,
                'type' => 'area',
                'fontFamily' => 'Inter, sans-serif',
                'toolbar' => ['show' => false]
            ],
            'series' => [
                [
                    'name' => 'Cost of Goods',
                    'data' => $cogsData['values'],
                    'color' => '#1A56DB'
                ]
            ],
            'xaxis' => [
                'categories' => $cogsData['labels'],
                'labels' => ['style' => ['fontSize' => '14px', 'fontWeight' => 500]]
            ],
            'yaxis' => [
                'labels' => [
                    'style' => ['fontSize' => '14px', 'fontWeight' => 500],
                    'formatter' => 'function (value) { return "$" + value.toFixed(2) }'
                ]
            ],
            'dataLabels' => ['enabled' => false],
            'stroke' => ['curve' => 'smooth', 'width' => 2],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'enabled' => true,
                    'opacityFrom' => 0.45,
                    'opacityTo' => 0
                ]
            ]
        ];

        $this->dispatch('updateCostChart', options: $this->chartOptions);
    }

    private function getCOGSData($startDate, $endDate)
    {
        $movements = InventoryMovement::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('transaction_type', [
                InventoryMovement::TRANSACTION_TYPE_ORDER_USED,
                InventoryMovement::TRANSACTION_TYPE_WASTE
            ])
            ->with('item')
            ->get()
            ->groupBy(function($movement) {
                return $movement->created_at->format('Y-m-d');
            })
            ->map(function($dayMovements) {
                return $dayMovements->sum(function($movement) {
                    // Calculate cost based on item's cost and quantity
                    return $movement->quantity * ($movement->item->cost ?? 0);
                });
            });

        return [
            'labels' => $movements->keys()->toArray(),
            'values' => $movements->values()->toArray()
        ];
    }

    public function render()
    {
        $movements = InventoryMovement::query()
            ->whereIn('transaction_type', [
                InventoryMovement::TRANSACTION_TYPE_ORDER_USED,
                InventoryMovement::TRANSACTION_TYPE_WASTE
            ])
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with(['item.unit'])
            ->latest()
            ->paginate(10);

        $totalCost = $movements->sum(function($movement) {
            return $movement->quantity * ($movement->item->cost ?? 0);
        });

        return view('inventory::livewire.reports.cost-analysis-report', [
            'movements' => $movements,
            'totalCost' => $totalCost
        ]);
    }
} 