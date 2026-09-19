<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessProductTranslation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;
    public $tries = 5;
    public $product;
    public $submittedLocales;

    public function __construct(Product $product, array $submittedLocales = [])
    {
        $this->product = $product;
        $this->submittedLocales = $submittedLocales;
    }

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
            } else {
                throw new \Exception('Groq AI returned invalid or empty JSON: ' . $content);
            }
        } else {
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
Please sanitize, polish, and translate the following product details into 6 languages: English (en), Arabic (ar), Spanish (es), German (de), Chinese (zh), and Turkish (tr). 

Original Name: {$this->product->name}
Original Short Description: {$this->product->short_description}
Original Full Description: {$this->product->full_description}

Requirements:
1. Fix any typos, messy formatting, and grammatical errors in the original text.
2. Ensure the tone is highly professional, engaging, and premium.
3. Return ONLY a valid JSON object matching the exact structure below.

Structure:
{
  "en": {
    "name": "...",
    "short_description": "...",
    "full_description": "..."
  },
  "ar": { "name": "...", "short_description": "...", "full_description": "..." },
  "es": { "name": "...", "short_description": "...", "full_description": "..." },
  "de": { "name": "...", "short_description": "...", "full_description": "..." },
  "zh": { "name": "...", "short_description": "...", "full_description": "..." },
  "tr": { "name": "...", "short_description": "...", "full_description": "..." }
}
PROMPT;
    }

    protected function saveTranslations(array $parsedContent): void
    {
        $locales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];

        foreach ($locales as $locale) {
            // Skip locales that the vendor explicitly submitted manually
            if (in_array($locale, $this->submittedLocales)) {
                continue;
            }

            if (isset($parsedContent[$locale])) {
                $translationData = $parsedContent[$locale];
                
                ProductTranslation::updateOrCreate(
                    [
                        'product_id' => $this->product->id,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $translationData['name'] ?? $this->product->name,
                        'short_description' => $translationData['short_description'] ?? $this->product->short_description,
                        'full_description' => $translationData['full_description'] ?? $this->product->full_description,
                    ]
                );
            }
        }
    }
}
