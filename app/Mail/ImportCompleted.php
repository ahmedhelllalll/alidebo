<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImportCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $stats;

    /**
     * Create a new message instance.
     *
     * @param array $stats
     */
    public function __construct(array $stats)
    {
        $this->stats = $stats;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Business Profiles Import Completed')
                    ->html($this->buildHtmlContent());
    }

    private function buildHtmlContent()
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #4CAF50;'>Import Completed Successfully</h2>
            <p>Your Excel import for business profiles has finished processing in the background.</p>
            <div style='background-color: #f9f9f9; padding: 15px; border-radius: 8px;'>
                <ul style='list-style-type: none; padding: 0;'>
                    <li style='margin-bottom: 10px;'><strong>Total Processed:</strong> {$this->stats['total']} rows</li>
                    <li style='margin-bottom: 10px;'><strong>Successfully Imported:</strong> {$this->stats['imported']} businesses</li>
                    <li style='margin-bottom: 10px; color: #f44336;'><strong>Skipped Rows:</strong> {$this->stats['skipped']} rows</li>
                    <li style='margin-bottom: 10px;'><strong>Auto-created Categories:</strong> {$this->stats['new_categories']}</li>
                    <li style='margin-bottom: 10px;'><strong>Auto-created Cities:</strong> {$this->stats['new_cities']}</li>
                </ul>
            </div>
            <p style='margin-top: 20px; font-size: 12px; color: #777;'>Alidebo Admin System</p>
        </div>
        ";
    }
}
