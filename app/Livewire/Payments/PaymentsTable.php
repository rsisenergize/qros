<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Helper\Common;

class PaymentsTable extends Component
{

    use WithPagination, WithoutUrlPagination;

    public $search;

    protected $listeners = ['refreshPayments' => '$refresh'];

    public function render()
    {


        $query = Payment::with('order:id,order_number')
            ->where('payment_method', '<>', 'due')
            ->where(function ($q) {

                $safeTerm = Common::safeString($this->search);

                return $q->where('amount', 'like', '%' . $safeTerm . '%')
                    ->orWhere('transaction_id', 'like', '%' . $safeTerm . '%')
                    ->orWhere('payment_method', 'like', '%' . $safeTerm . '%')
                    ->orWhereHas('order', function ($q) use ($safeTerm) {
                        $q->where('order_number', 'like', '%' . $safeTerm . '%');
                    });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.payments.payments-table', [
            'payments' => $query
        ]);
    }
}
