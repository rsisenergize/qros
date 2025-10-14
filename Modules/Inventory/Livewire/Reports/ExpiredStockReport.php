<?php

namespace Modules\Inventory\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Modules\Inventory\Entities\InventoryStock;

#[Layout('layouts.app')]
class ExpiredStockReport extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $chartOptions = [];
    public $filterDays = 30; // Default to show items expiring in next 30 days

    public function mount()
    {
        $this->loadReportData();
    }

    public function loadReportData()
    {
        $expiryData = $this->getExpiryData();

        $this->chartOptions = [
            'chart' => [
                'height' => 420,
                'type' => 'bar',
                'fontFamily' => 'Inter, sans-serif',
                'toolbar' => ['show' => false]
            ],
            'series' => [[
                'name' => 'Expiring Items',
                'data' => $expiryData['values'],
                'color' => '#EF4444'
            ]],
            'xaxis' => [
                'categories' => $expiryData['labels'],
                'labels' => ['style' => ['fontSize' => '14px', 'fontWeight' => 500]]
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 4,
                    'horizontal' => false,
                ]
            ]
        ];

        $this->dispatch('updateExpiryChart', options: $this->chartOptions);
    }

    private function getExpiryData()
    {
        $today = Carbon::today();
        $endDate = Carbon::today()->addDays($this->filterDays);

        $stocks = InventoryStock::query()
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $endDate])
            ->with('item')
            ->get()
            ->groupBy(function($stock) {
                return Carbon::parse($stock->expiry_date)->format('Y-m-d');
            });

        return [
            'labels' => $stocks->keys()->toArray(),
            'values' => $stocks->map->sum('quantity')->values()->toArray()
        ];
    }

    public function render()
    {
        $today = Carbon::today();
        $stocks = InventoryStock::query()
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', $today)
            ->where('quantity', '>', 0)
            ->when($this->searchTerm, function($query) {
                $query->whereHas('item', function($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->with(['item.unit'])
            ->orderBy('expiry_date')
            ->paginate(10);

        $totalExpiring = $stocks->sum('quantity');
        $totalValue = $stocks->sum(function($stock) {
            return $stock->quantity * ($stock->item->cost ?? 0);
        });

        return view('inventory::livewire.reports.expired-stock-report', [
            'stocks' => $stocks,
            'totalExpiring' => $totalExpiring,
            'totalValue' => $totalValue
        ]);
    }
} 