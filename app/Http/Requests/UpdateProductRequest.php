<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.*' => 'nullable|string|max:255',
            
            'short_description' => 'required|array',
            'short_description.en' => 'required|string|max:255',
            'short_description.*' => 'nullable|string|max:255',
            
            'full_description' => 'nullable|array',
            'full_description.*' => 'nullable|string',

            'unit' => 'required|string|in:piece,kg,carton,box,meter,set,ton,liter',
            'units_per_package' => 'nullable|integer|min:1|max:1000000',
            'sku' => 'nullable|string|max:100',
            'moq' => 'nullable|integer|min:1',
            'base_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:EGP,USD',
            'category_id' => 'nullable|exists:product_categories,id',
            
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            
            'specifications' => 'nullable|array',
            'specifications.*.name' => 'required_with:specifications|array',
            'specifications.*.name.en' => 'required_with:specifications|string',
            'specifications.*.value' => 'nullable|array',
        ];
    }
}
