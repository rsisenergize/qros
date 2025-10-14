<?php

namespace Modules\Inventory\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\BaseNotification;
use Modules\Inventory\Entities\PurchaseOrder;

class SendPurchaseOrder extends BaseNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public PurchaseOrder $purchaseOrder) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(trans('inventory::modules.purchaseOrder.purchase_order_received', ['po_number' => $this->purchaseOrder->po_number]))
            ->view('inventory::emails.purchase-order', [
                'purchaseOrder' => $this->purchaseOrder,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
