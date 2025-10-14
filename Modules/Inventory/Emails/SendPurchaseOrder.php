<?php

namespace Modules\Inventory\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Inventory\Entities\PurchaseOrder;

class SendPurchaseOrder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public PurchaseOrder $purchaseOrder) {}

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->view('inventory::emails.purchase-order')
            ->with([
                'purchaseOrder' => $this->purchaseOrder,
            ]);
    }
}
