<?php

namespace App\Livewire\Dashboard;

use App\Models\WaiterRequest;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class ActiveWaiterRequests extends Component
{

    use LivewireAlert;

    protected $listeners = ['attended' => 'attended', 'newWaiterRequest' => 'handleNewWaiterRequest', 'waiterRequestCreated' => 'handleNewWaiterRequest'];

    public function mount()
    {
        if (!session()->has('active_waiter_requests_count')) {
            $count = WaiterRequest::where('status', 'pending')->distinct('table_id')->count();
            session(['active_waiter_requests_count' => $count]);
        }
    }

    public function handleNewWaiterRequest($data = null)
    {
        $recentRequest = WaiterRequest::where('status', 'pending')->latest()->first();

            if ($recentRequest) {
                // Play sound immediately
                $this->js('new Audio("' . asset('sound/new_order.wav') . '").play()');

                $this->confirm(__('modules.waiterRequest.newWaiterRequestForTable', ['name' => $recentRequest->table->table_code]), [
                    'position' => 'center',
                    'confirmButtonText' => __('modules.waiterRequest.markCompleted'),
                    'confirmButtonColor' => '#16a34a',
                    'onConfirmed' => 'attended',
                    'showCancelButton' => true,
                    'cancelButtonText' => __('modules.waiterRequest.doItLater'),
                    'onCanceled' => 'doItLater',
                    'data' => [
                        'tableID' => $recentRequest->table_id
                    ]
                ]);
            }

        $count = WaiterRequest::where('status', 'pending')->distinct('table_id')->count();
        session(['active_waiter_requests_count' => $count]);

        $this->dispatch('$refresh');
    }

    public function attended($data)
    {
        WaiterRequest::where('table_id', $data['tableID'])->update(['status' => 'completed']);

        $count = WaiterRequest::where('status', 'pending')->distinct('table_id')->count();
        session(['active_waiter_requests_count' => $count]);

        $this->dispatch('$refresh');
    }

    public function doItLater()
    {
        return $this->redirect(route('waiter-requests.index'), navigate: true);
    }

    public function render()
    {
        $count = WaiterRequest::where('status', 'pending')->distinct('table_id')->count();

        if (session()->has('active_waiter_requests_count') && session('active_waiter_requests_count') < $count) {

            $recentRequest = WaiterRequest::where('status', 'pending')->latest()->first();

            if ($recentRequest) {
                // Play sound immediately
                $this->js('new Audio("' . asset('sound/new_order.wav') . '").play()');

                $this->confirm(__('modules.waiterRequest.newWaiterRequestForTable', ['name' => $recentRequest->table->table_code]), [
                    'position' => 'center',
                    'confirmButtonText' => __('modules.waiterRequest.markCompleted'),
                    'confirmButtonColor' => '#16a34a',
                    'onConfirmed' => 'attended',
                    'showCancelButton' => true,
                    'cancelButtonText' => __('modules.waiterRequest.doItLater'),
                    'onCanceled' => 'doItLater',
                    'data' => [
                        'tableID' => $recentRequest->table_id
                    ]
                ]);
            }

            session(['active_waiter_requests_count' => $count]);
        }

        return view('livewire.dashboard.active-waiter-requests', [
            'count' => $count
        ]);
    }

    public function refreshActiveWaiterRequests()
    {
        $count = WaiterRequest::where('status', 'pending')->distinct('table_id')->count();

        // Check if there's a new waiter request
        if (session()->has('active_waiter_requests_count') && session('active_waiter_requests_count') < $count) {
            $recentRequest = WaiterRequest::where('status', 'pending')->latest()->first();

            if ($recentRequest) {
                // Play sound immediately
                $this->js('new Audio("' . asset('sound/new_order.wav') . '").play()');

                $this->confirm(__('modules.waiterRequest.newWaiterRequestForTable', ['name' => $recentRequest->table->table_code]), [
                    'position' => 'center',
                    'confirmButtonText' => __('modules.waiterRequest.markCompleted'),
                    'confirmButtonColor' => '#16a34a',
                    'onConfirmed' => 'attended',
                    'showCancelButton' => true,
                    'cancelButtonText' => __('modules.waiterRequest.doItLater'),
                    'onCanceled' => 'doItLater',
                    'data' => [
                        'tableID' => $recentRequest->table_id
                    ]
                ]);
            }

            session(['active_waiter_requests_count' => $count]);
        }

        $this->dispatch('$refresh');
    }
}
