<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BusinessView;

class RecordBusinessView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $businessProfileId;
    public $ip;
    public $userAgent;
    public $countryCode;

    /**
     * Create a new job instance.
     */
    public function __construct($businessProfileId, $ip, $userAgent, $countryCode = null)
    {
        $this->businessProfileId = $businessProfileId;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->countryCode = $countryCode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $alreadyViewedToday = BusinessView::where('business_profile_id', $this->businessProfileId)
            ->where('ip_address', $this->ip)
            ->where('user_agent', $this->userAgent)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if (!$alreadyViewedToday) {
            BusinessView::create([
                'business_profile_id' => $this->businessProfileId,
                'ip_address' => $this->ip,
                'user_agent' => $this->userAgent,
                'country_code' => $this->countryCode
            ]);
        }
    }
}
