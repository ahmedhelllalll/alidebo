<?php

namespace App\Services;

class DataNormalizationService
{
    /**
     * Normalize string for comparison.
     * Removes spaces, normalizes Arabic characters, and converts to lowercase.
     */
    public function normalize(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Convert to lowercase (for English)
        $text = mb_strtolower($text, 'UTF-8');

        // Remove common punctuation and special characters
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);

        // Remove Arabic diacritics (Tashkeel)
        $text = preg_replace('/[\x{0617}-\x{061A}\x{064B}-\x{0652}]/u', '', $text);

        // Normalize Alef
        $text = preg_replace('/[\x{0622}\x{0623}\x{0625}]/u', 'ا', $text);

        // Normalize Ta-marbuta to Ha
        $text = preg_replace('/\x{0629}/u', 'ه', $text);

        // Normalize Ya to Alef Maksura (or vice versa depending on preference, we use ي)
        $text = preg_replace('/\x{0649}/u', 'ي', $text);

        // Remove all whitespace
        $text = preg_replace('/\s+/u', '', $text);

        return $text;
    }

    /**
     * Fuzzy match a search string against a collection of models.
     * Searches across multiple localized fields if available.
     * 
     * @param string $search
     * @param \Illuminate\Support\Collection $collection
     * @param array $fields Fields to check (e.g. ['name_ar', 'name_en'])
     * @return mixed Matching model or null
     */
    public function match(string $search, $collection, array $fields = ['name_ar', 'name_en'])
    {
        $normalizedSearch = self::normalize($search);

        if (empty($normalizedSearch)) {
            return null;
        }

        // Exact normalized match
        foreach ($collection as $item) {
            foreach ($fields as $field) {
                if (!empty($item->$field)) {
                    $normalizedItem = self::normalize($item->$field);
                    if ($normalizedSearch === $normalizedItem) {
                        return $item;
                    }
                }
            }
        }

        // Optional: Loose match (contains or levenshtein) if needed later
        foreach ($collection as $item) {
            foreach ($fields as $field) {
                if (!empty($item->$field)) {
                    $normalizedItem = self::normalize($item->$field);
                    // Check if one contains the other (for cases like "Cairo Governorate" vs "Cairo")
                    if (str_contains($normalizedItem, $normalizedSearch) || str_contains($normalizedSearch, $normalizedItem)) {
                        return $item;
                    }
                }
            }
        }

        return null;
    }
}
