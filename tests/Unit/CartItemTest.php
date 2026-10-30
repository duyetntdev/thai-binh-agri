<?php

namespace Tests\Unit;

use App\Cart\CartItem;
use PHPUnit\Framework\TestCase;

class CartItemTest extends TestCase
{
    public function test_subtotal_and_quantity_copy_preserve_item_details(): void
    {
        $item = new CartItem(12, 'Rice', 'rice', 25.5, 3, 'rice.jpg');
        $updated = $item->withQuantity(4);

        $this->assertSame(76.5, $item->subtotal());
        $this->assertSame(102.0, $updated->subtotal());
        $this->assertSame(3, $item->quantity);
        $this->assertSame('rice.jpg', $updated->thumbnail);
    }

    public function test_cart_item_round_trips_through_array_representation(): void
    {
        $item = new CartItem(12, 'Rice', 'rice', 25.5, 3, null);

        $this->assertEquals($item, CartItem::fromArray($item->toArray()));
    }
}
