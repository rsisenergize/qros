<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\{
    Payment,
    Restaurant,
    Order,
    AdminPayfastPayment
};

use App\Events\SendNewOrderReceived;
use App\Events\SendOrderBillEvent;


class PayfastPaymentController extends Controller
{

    public function paymentMainSuccess(Request $request)
    {
        $reference = $request->reference;

        if (!$reference) {
            return $this->flashAndRedirect('No reference supplied!', 'danger');
        }

        $payfastPayment = AdminPayfastPayment::where('payfast_payment_id', $reference)->first();

        if (!$payfastPayment) {
            return $this->flashAndRedirect('Payment record not found!', 'danger');
        }

        $orderUuid = $payfastPayment->order->uuid ?? null;

        switch ($payfastPayment->payment_status) {
            case 'completed':
                $this->markPaymentAsPaid($payfastPayment);
                return $this->flashAndRedirect('Payment processed successfully!', 'success', $orderUuid);

            case 'pending':
                return $this->flashAndRedirect('Payment is still pending confirmation from PayFast.', 'info');

            default:
                return $this->flashAndRedirect('Payment failed or was cancelled.', 'danger', $orderUuid);
        }
    }

    public function paymentFailed(Request $request)
    {
        $reference = $request->input('reference') ?? $request->input('m_payment_id');

        if ($reference) {
            $payment = AdminPayfastPayment::where('payfast_payment_id', $reference)->first();
            if ($payment) {
                $payment->update([
                    'payment_status' => 'failed',
                    'payment_error_response' => json_encode([
                        'message' => 'User cancelled or PayFast failed to process the payment.'
                    ])
                ]);
            }
        }

        return $this->flashAndRedirect('PayFast payment was cancelled or failed.', 'danger');
    }

    public function payfastNotify(Request $request, $company, $reference)
    {
        $data = $request->except('signature');
        $restaurant = Restaurant::where('hash', $company)->first();

        if (!$restaurant) {
            return response('Invalid restaurant', 404);
        }

        $status = $data['payment_status'] ?? 'failed';
        $amountGross = $data['amount_gross'] ?? 0;
        $payfastId = $data['pf_payment_id'] ?? null;

        if (strtoupper($status) === 'COMPLETE') {
            $payment = AdminPayfastPayment::where('payfast_payment_id', $reference)->first();

            if ($payment) {
                $payment->update([
                    'payment_status' => 'completed',
                    'payment_error_response' => json_encode(['message' => 'Payment completed successfully.']),
                ]);


                Payment::updateOrCreate(
                    [
                        'order_id' => $payment->order_id,
                        'payment_method' => 'due',
                        'amount' => $payment->amount
                    ],
                    [
                        'payment_method' => 'payfast',
                        'branch_id' => $payment->order->branch_id,
                        'transaction_id' => $payfastId,
                    ]
                );

                $this->markPaymentAsPaid($payment, $amountGross);

            }
        }

        return response('OK', 200);
    }

    /**
     * Flash message and redirect to maintenance index
     */
    private function flashAndRedirect(string $message, string $style, $orderId = null)
    {
        session()->flash('flash.banner', $message);
        session()->flash('flash.bannerStyle', $style);
        return $orderId
            ? redirect()->route('order_success', $orderId)
            : redirect()->back();
    }

    /**
     * Update order as paid
     */
    private function markPaymentAsPaid(AdminPayfastPayment $payfastPayment, $amount = null)
    {
        $order = Order::find($payfastPayment->order_id);

        if ($order) {
            $order->update([
                'amount_paid' =>$order->amount_paid + $payfastPayment->amount,
                'status' => 'paid',
            ]);
        }
            SendNewOrderReceived::dispatch($order);

            if ($order->customer_id) {
                SendOrderBillEvent::dispatch($order);
            }
    }
}
