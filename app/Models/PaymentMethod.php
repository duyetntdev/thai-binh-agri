<?php

namespace App\Models;

enum PaymentMethod: string
{
    case VNPAY        = 'vnpay';
    case COD          = 'cod';
    case BANK_TRANSFER = 'bank_transfer';

    public function label(): string
    {
        return match($this) {
            self::VNPAY         => 'VNPay',
            self::COD           => 'Thanh toán khi nhận hàng (COD)',
            self::BANK_TRANSFER => 'Chuyển khoản ngân hàng',
        };
    }
}
