<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $product;
    public $reason;

    public function __construct(Product $product, $reason)
    {
        $this->product = $product;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(__('admin.product_rejected_subject', [], 'en', 'Action Required: Your Product Was Not Approved'))
                    ->line(__('admin.product_rejected_line1', ['name' => $this->product->name], 'en', 'We reviewed your product "' . $this->product->name . '" but unfortunately it was not approved at this time.'))
                    ->line(__('admin.product_rejected_reason', ['reason' => $this->reason], 'en', 'Reason: ' . $this->reason))
                    ->action(__('admin.product_rejected_action', [], 'en', 'Update Product'), url('/business/products/' . $this->product->id . '/edit'))
                    ->line(__('admin.product_rejected_line2', [], 'en', 'Please update the details and submit again.'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'product_rejected',
            'product_id' => $this->product->id,
            'message' => __('admin.product_rejected_notif', ['name' => $this->product->name], 'en', 'Your product "' . $this->product->name . '" was rejected.'),
            'reason' => $this->reason,
        ];
    }
}
