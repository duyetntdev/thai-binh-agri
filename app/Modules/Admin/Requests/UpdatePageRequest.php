<?php

namespace App\Modules\Admin\Requests;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        /** @var Page $page */
        $page = $this->route('page');

        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'alpha_dash', Rule::unique('pages', 'slug')->ignore($page)],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
