<?php

namespace App\Models;

enum PaymentStatus: string
{
    case PENDING   = 'pending';
    case COMPLETED = 'completed';
    case FAILED    = 'failed';
    case REFUNDED  = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING   => 'Chờ thanh toán',
            self::COMPLETED => 'Đã thanh toán',
            self::FAILED    => 'Thanh toán thất bại',
            self::REFUNDED  => 'Đã hoàn tiền',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING   => 'yellow',
            self::COMPLETED => 'green',
            self::FAILED    => 'red',
            self::REFUNDED  => 'purple',
        };
    }
}
