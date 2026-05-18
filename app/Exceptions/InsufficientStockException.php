<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(string $productName, int $requested, int $available)
    {
        parent::__construct(
            "Sản phẩm '{$productName}' không đủ tồn kho. Yêu cầu: {$requested}, còn lại: {$available}."
        );
    }
}
