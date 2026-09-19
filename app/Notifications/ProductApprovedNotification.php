<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(__('admin.product_approved_subject', [], 'en', 'Your Product Has Been Approved'))
                    ->line(__('admin.product_approved_line1', ['name' => $this->product->name], 'en', 'Great news! Your product "' . $this->product->name . '" has been approved and is now live on our platform.'))
                    ->action(__('admin.product_approved_action', [], 'en', 'View Product'), url('/' . $this->product->businessProfile->slug . '/products'))
                    ->line(__('admin.product_approved_line2', [], 'en', 'Thank you for using our application!'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'product_approved',
            'product_id' => $this->product->id,
            'message' => __('admin.product_approved_notif', ['name' => $this->product->name], 'en', 'Your product "' . $this->product->name . '" has been approved.'),
        ];
    }
}
