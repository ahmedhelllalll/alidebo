<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Enforced via middleware globally
    }

    public function rules(): array
    {
        return [
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_de' => ['nullable', 'string', 'max:255'],
            'name_es' => ['nullable', 'string', 'max:255'],
            'name_tr' => ['nullable', 'string', 'max:255'],
            'name_zh' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'status' => ['required', 'in:active,pending'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
            'icon' => ['nullable', 'string', 'max:255'],
        ];
    }
}
