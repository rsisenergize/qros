<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\On;
use App\Exports\CustomerExport;
use App\Imports\CustomerImport;
use App\Imports\CustomersImport;
use App\Jobs\ImportCustomerDataJob;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class CustomerList extends Component
{
    use WithFileUploads;
    use LivewireAlert;
    public $search;
    public $showAddCustomer;
    public $file;
    public $showImportCustomer;

    public function exportCustomerList()
    {

        if (!in_array('Export Report', restaurant_modules())) {
            $this->dispatch('showUpgradeLicense');
        }
        else
        {
            return Excel::download(new CustomerExport, 'customers-' . now()->toDateTimeString() . '.xlsx');
        }
    }


    #[On('closeAddCustomer')]
    public function closeAddCustomer()
    {
        $this->showAddCustomer = false;
    }

    public function closeImportCustomer()
    {
        $this->reset(['file', 'showImportCustomer']);
    }

    public function refreshCustomerList()
    {
        $this->dispatch('refreshCustomers');
    }

    public function importCustomerList()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,csv|max:10240',
        ]);

        $filePath = $this->file->store('customer-imports');
        Bus::dispatch(new ImportCustomerDataJob($filePath, restaurant()->id));

        $this->alert('success', __('messages.customerImportStarted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        // Reset component state
        $this->reset(['file', 'showImportCustomer']);

        // Dispatch refresh event to update the customer table
        $this->dispatch('refreshCustomers');

        // Reset file input in the view
        $this->dispatch('reset-file-input');

        // Force re-render of the component
        $this->render();
    }

    public function render()
    {
        return view('livewire.customer.customer-list');
    }

}
