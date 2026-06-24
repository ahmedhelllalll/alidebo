<?php

namespace App\Notifications;

use App\Models\BusinessProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $business;

    public function __construct(BusinessProfile $business)
    {
        $this->business = $business;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(__('admin.business_approved_subject'))
                    ->line(__('admin.business_approved_line1', ['name' => $this->business->name]))
                    ->action(__('admin.business_approved_action'), url('/' . $this->business->slug))
                    ->line(__('admin.business_approved_line2'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'business_approved',
            'business_id' => $this->business->id,
            'message' => __('admin.business_approved_notif', ['name' => $this->business->name]),
        ];
    }
}
