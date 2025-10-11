<?php

namespace App\Livewire\Billing;

use Livewire\Component;
use App\Models\GlobalInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithPagination;
use App\Helper\Common;

class InvoiceTable extends Component
{
    use WithPagination;
    public $search;
    public $restaurantId;

    public function downloadReceipt($id)
    {
        $invoice = GlobalInvoice::findOrFail($id);

        if (!$invoice) {

            $this->alert('error', __('messages.noInvoiceFound'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            return;
        }


        $pdf = Pdf::loadView('billing.billing-receipt', ['invoice' => $invoice]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'billing-receipt-' . uniqid() . '.pdf');
    }

    public function render()
    {
        $query = GlobalInvoice::query()
            ->with(['restaurant', 'package'])
            ->orderByDesc('id');

        if ($this->search) {
            $query->where(function ($q) {
                $safeTerm = Common::safeString($this->search);
                $q->whereHas('restaurant', function ($q) use ($safeTerm) {
                    $q->where('name', 'like', '%' . $safeTerm . '%');
                })
                    ->orWhereHas('package', function ($q) use ($safeTerm) {
                        $q->where('package_type', 'like', '%' . $safeTerm . '%');
                    })
                    ->orWhere('gateway_name', 'like', '%' . $safeTerm . '%')
                    ->orWhere('total', 'like', '%' . $safeTerm . '%')
                    ->orWhere('transaction_id', 'like', '%' . $safeTerm . '%')
                    ->orWhere('package_type', 'like', '%' . $safeTerm . '%');
            });
        }

        if ($this->restaurantId) {
            $query->where('restaurant_id', $this->restaurantId);
        }

        $invoices = $query->paginate(10);

        return view('livewire.billing.invoice-table', [
            'invoices' => $invoices
        ]);
    }
}
