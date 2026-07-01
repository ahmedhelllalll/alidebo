<?php

namespace App\Jobs;

use App\Models\BusinessProfile;
use App\Models\BusinessProfileTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OptimizeProfileContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;
    public $tries = 5;
    public $businessProfile;

    /**
     * Create a new job instance.
     */
    public function __construct(BusinessProfile $businessProfile)
    {
        $this->businessProfile = $businessProfile;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $apiKey = config('services.groq.optimization_api_key');
        if (!$apiKey || $apiKey === '[REDACTED]') {
            throw new \Exception('Groq Optimization API Key is missing or invalid in config.');
        }

        $prompt = $this->buildPrompt();

        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert multi-lingual copywriter and SEO specialist. Return ONLY a valid JSON object. Do not include any explanations.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.7,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            
            $parsedContent = json_decode($content, true);
            
            if (is_array($parsedContent) && !empty($parsedContent)) {
                $this->saveTranslations($parsedContent);
                
                // Introduce a 15-second delay to pace the worker and respect Groq's rate limits
                sleep(15);
            } else {
                throw new \Exception('Groq AI returned invalid or empty JSON: ' . $content);
            }
        } else {
            // Auto-Retry Safety Net: If Groq hits a rate limit (429), wait 60 seconds and try again
            if ($response->status() === 429) {
                $this->release(60);
                return;
            }
            
            throw new \Exception('Groq API Error: ' . $response->status() . ' - ' . $response->body());
        }
    }

    protected function buildPrompt(): string
    {
        return <<<PROMPT
Please sanitize, polish, and translate the following business profile into 6 languages: English (en), Arabic (ar), Spanish (es), German (de), Chinese (zh), and Turkish (tr). 

Original Name: {$this->businessProfile->name}
Original Description: {$this->businessProfile->description}

Requirements:
1. Fix any typos, messy formatting, and grammatical errors in the original text.
2. Ensure the tone is highly professional, engaging, and premium.
3. For each language, generate an SEO optimized 'meta_title' (max 60 characters) and 'meta_description' (max 160 characters) based on the profile.
4. Return ONLY a valid JSON object matching the exact structure below.

Structure:
{
  "en": {
    "name": "...",
    "description": "...",
    "meta_title": "...",
    "meta_description": "..."
  },
  "ar": {
    "name": "...",
    "description": "...",
    "meta_title": "...",
    "meta_description": "..."
  },
  "es": { "name": "...", "description": "...", "meta_title": "...", "meta_description": "..." },
  "de": { "name": "...", "description": "...", "meta_title": "...", "meta_description": "..." },
  "zh": { "name": "...", "description": "...", "meta_title": "...", "meta_description": "..." },
  "tr": { "name": "...", "description": "...", "meta_title": "...", "meta_description": "..." }
}
PROMPT;
    }

    protected function saveTranslations(array $parsedContent): void
    {
        $locales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];

        foreach ($locales as $locale) {
            if (isset($parsedContent[$locale])) {
                $translationData = $parsedContent[$locale];
                
                BusinessProfileTranslation::updateOrCreate(
                    [
                        'business_profile_id' => $this->businessProfile->id,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $translationData['name'] ?? $this->businessProfile->name,
                        'description' => $translationData['description'] ?? $this->businessProfile->description,
                        'meta_title' => $translationData['meta_title'] ?? null,
                        'meta_description' => $translationData['meta_description'] ?? null,
                    ]
                );
            }
        }
    }
}
