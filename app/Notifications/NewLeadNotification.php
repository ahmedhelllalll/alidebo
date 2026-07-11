<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\BusinessLead;

class NewLeadNotification extends Notification
{
    use Queueable;

    protected BusinessLead $lead;

    public function __construct(BusinessLead $lead)
    {
        $this->lead = $lead;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'lead_id' => $this->lead->id,
            'name' => $this->lead->name,
            'email' => $this->lead->email,
            'phone' => $this->lead->phone,
            'business_id' => $this->lead->business_profile_id,
        ];
    }
}
