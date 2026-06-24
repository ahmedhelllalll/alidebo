<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('social_links') && is_array($this->social_links)) {
            $links = $this->social_links;
            $urlFields = ['website', 'facebook', 'linkedin', 'youtube'];
            foreach ($urlFields as $field) {
                if (!empty($links[$field]) && !preg_match("~^(?:f|ht)tps?://~i", $links[$field])) {
                    $links[$field] = "https://" . $links[$field];
                }
            }
            $this->merge(['social_links' => $links]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'slug' => 'required|string|max:255|unique:business_profiles,slug',
            'category_id' => 'required_without:custom_category_name|nullable|exists:categories,id',
            'custom_category_name' => 'required_without:category_id|nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'custom_country_name' => 'nullable|string|max:255',
            'city_id' => 'required_without:custom_city_name|nullable|exists:cities,id',
            'custom_city_name' => 'required_without:city_id|nullable|string|max:255',
            'description' => 'required|string|min:75|max:500',
            'address' => 'nullable|string',
            'social_links' => 'nullable|array',
            'social_links.whatsapp' => 'nullable|string|max:255',
            'social_links.phone' => 'nullable|string|max:255',
            'social_links.website' => 'nullable|url|max:255',
            'social_links.facebook' => 'nullable|url|max:255',
            'social_links.instagram' => 'nullable|string|max:255',
            'social_links.tiktok' => 'nullable|string|max:255',
            'social_links.linkedin' => 'nullable|url|max:255',
            'social_links.twitter' => 'nullable|string|max:255',
            'social_links.youtube' => 'nullable|url|max:255',
            'social_links.telegram' => 'nullable|string|max:255',
            'social_links.snapchat' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}
