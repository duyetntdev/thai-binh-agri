<?php

namespace App\Modules\Cart\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Product $product */
        $product = $this->route('product');
        $quantity = ['sometimes', 'integer', 'min:1'];

        if ($product->stock > 0) {
            $quantity[] = 'max:'.$product->stock;
        }

        return ['quantity' => $quantity];
    }
}
