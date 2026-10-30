<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrdersShowViewTest extends TestCase
{
    public function test_order_detail_view_is_registered(): void
    {
        $this->assertTrue(view()->exists('orders::show'));
    }
}