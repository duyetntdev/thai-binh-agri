<?php

namespace App\Models;

enum OrderStatus: string
{
    case PENDING    = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED    = 'shipped';
    case DELIVERED  = 'delivered';
    case CANCELLED  = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING    => 'Chờ xác nhận',
            self::PROCESSING => 'Đang xử lý',
            self::SHIPPED    => 'Đang giao hàng',
            self::DELIVERED  => 'Đã giao',
            self::CANCELLED  => 'Đã hủy',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING    => 'yellow',
            self::PROCESSING => 'blue',
            self::SHIPPED    => 'indigo',
            self::DELIVERED  => 'green',
            self::CANCELLED  => 'red',
        };
    }

    public function canCancel(): bool
    {
        return in_array($this, [self::PENDING, self::PROCESSING]);
    }
}
