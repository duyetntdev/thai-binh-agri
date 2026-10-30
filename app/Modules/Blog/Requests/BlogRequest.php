<?php

namespace App\Modules\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
