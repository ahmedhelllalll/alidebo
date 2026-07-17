<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SubmitUrlToGoogleIndex implements ShouldQueue
{
    use Queueable;

    protected $log;

    /**
     * Create a new job instance.
     */
    public function __construct(\App\Models\GoogleIndexLog $log)
    {
        $this->log = $log;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\GoogleIndexingService $service): void
    {
        $response = $service->updateUrl($this->log->url);
        
        if ($response['success']) {
            $this->log->update([
                'status' => 'submitted',
                'response' => json_encode($response['response'])
            ]);
        } else {
            $this->log->update([
                'status' => 'failed',
                'response' => $response['message']
            ]);
        }
    }
}
