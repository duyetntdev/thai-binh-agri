<?php

namespace App\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class MenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:120'],
            'url' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
