<?php

namespace App\Modules\Orders\Requests;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'notes' => ['nullable', 'string', 'max:500'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+().\s-]+$/'],
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'ward_id' => [
                'required',
                'integer',
                Rule::exists('wards', 'id')->where('province_id', $this->input('province_id')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Giỏ hàng trống.',
            'items.*.product_id.exists' => 'Sản phẩm không tồn tại.',
            'items.*.quantity.min' => 'Số lượng tối thiểu là 1.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng.',
            'shipping_phone.required' => 'Vui lòng nhập số điện thoại nhận hàng.',
            'shipping_phone.regex' => 'Số điện thoại nhận hàng không hợp lệ.',
            'province_id.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'ward_id.required' => 'Vui lòng chọn phường/xã.',
            'ward_id.exists' => 'Phường/xã không thuộc tỉnh/thành phố đã chọn.',
        ];
    }
}
