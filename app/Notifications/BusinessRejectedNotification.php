<?php

namespace App\Notifications;

use App\Models\BusinessProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $business;
    public $reason;

    public function __construct(BusinessProfile $business, $reason)
    {
        $this->business = $business;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(__('admin.business_rejected_subject'))
                    ->line(__('admin.business_rejected_line1', ['name' => $this->business->name]))
                    ->line(__('admin.business_rejected_line2'))
                    ->line($this->reason)
                    ->action(__('admin.business_rejected_action'), url('/dashboard/business/edit'))
                    ->line(__('admin.business_rejected_line3'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'business_rejected',
            'business_id' => $this->business->id,
            'message' => __('admin.business_rejected_notif', ['name' => $this->business->name, 'reason' => $this->reason]),
        ];
    }
}
