<?php

namespace Tests\Unit;

use App\Models\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_only_pending_and_processing_orders_can_be_cancelled(): void
    {
        $this->assertTrue(OrderStatus::PENDING->canCancel());
        $this->assertTrue(OrderStatus::PROCESSING->canCancel());
        $this->assertFalse(OrderStatus::SHIPPED->canCancel());
        $this->assertFalse(OrderStatus::DELIVERED->canCancel());
        $this->assertFalse(OrderStatus::CANCELLED->canCancel());
    }

    public function test_each_order_status_has_a_customer_label_and_color(): void
    {
        foreach (OrderStatus::cases() as $status) {
            $this->assertNotSame('', $status->label());
            $this->assertNotSame('', $status->color());
        }
    }
}
