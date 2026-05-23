<?php

namespace App\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
