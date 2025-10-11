<?php

namespace App\Livewire\WaiterRequest;

use Livewire\Component;
use App\Models\Area;
use App\Models\Table;
use App\Models\WaiterRequest;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Tables extends Component
{
    use LivewireAlert;

    protected $listeners = ['waiterRequestCreated' => 'render'];

    public $pollingEnabled = true;
    public $pollingInterval = 10;

    public function mount()
    {
        // Load polling settings from cookies
        $this->pollingEnabled = filter_var(request()->cookie('waiter_request_polling_enabled', 'true'), FILTER_VALIDATE_BOOLEAN);
        $this->pollingInterval = (int)request()->cookie('waiter_request_polling_interval', 10);
    }



    public function updatedPollingEnabled($value)
    {
        cookie()->queue(cookie('waiter_request_polling_enabled', $value ? 'true' : 'false', 60 * 24 * 30)); // 30 days
    }

    public function updatedPollingInterval($value)
    {
        cookie()->queue(cookie('waiter_request_polling_interval', (int)$value, 60 * 24 * 30)); // 30 days
    }



    public function showTableOrder($id)
    {
        return $this->redirect(route('pos.show', $id), navigate: true);
    }

    public function showTableOrderDetail($id)
    {
        return $this->redirect(route('pos.order', [$id]), navigate: true);
    }

    public function markCompleted($id)
    {
        $waiterRequest = WaiterRequest::findOrFail($id);
        WaiterRequest::where('table_id', $waiterRequest->table_id)->update(['status' => 'completed']);

        $count = WaiterRequest::where('status', 'pending')->count();
        session(['active_waiter_requests_count' => $count]);

        $this->dispatch('$refresh');

        $this->alert('success', __('messages.waiterRequestCompleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

    }

    public function render()
    {
        $query = Area::with(['tables' => function ($query) {
            return $query->whereHas('activeWaiterRequest');
        }, 'tables.waiterRequests', 'tables.activeOrder']);

        $query = $query->get();


        return view('livewire.waiter-request.tables', [
            'tables' => $query,
            'areas' => Area::get()
        ]);
    }
}
