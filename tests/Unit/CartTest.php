<?php

namespace Tests\Unit;

use App\Cart\Cart;
use App\Models\Product;
use Tests\TestCase;

class CartTest extends TestCase
{
    public function test_add_increases_quantity_but_never_above_stock(): void
    {
        $product = $this->product(stock: 5);
        $cart = new Cart;

        $cart->add($product, 3);
        $cart->add($product, 4);

        $this->assertSame(5, $cart->count());
        $this->assertSame(5, $cart->get($product->id)->quantity);
        $this->assertSame(50.0, $cart->total());
    }

    public function test_add_ignores_invalid_quantity_and_unavailable_products(): void
    {
        $cart = new Cart;

        $cart->add($this->product(stock: 5), 0);
        $cart->add($this->product(stock: 0), 1);
        $cart->add($this->product(stock: 5, status: 'inactive'), 1);

        $this->assertTrue($cart->isEmpty());
    }

    public function test_update_quantity_is_limited_to_current_stock(): void
    {
        $product = $this->product(stock: 8);
        $cart = new Cart;
        $cart->add($product, 2);

        $cart->updateQuantity($product->id, 7, 4);

        $this->assertSame(4, $cart->get($product->id)->quantity);
        $this->assertSame(40.0, $cart->total());
    }

    public function test_update_removes_item_when_stock_is_gone_or_quantity_is_zero(): void
    {
        $product = $this->product(stock: 3);
        $cart = new Cart;
        $cart->add($product);
        $cart->updateQuantity($product->id, 2, 0);

        $this->assertTrue($cart->isEmpty());

        $cart->add($product);
        $cart->updateQuantity($product->id, 0, 3);

        $this->assertTrue($cart->isEmpty());
    }

    public function test_cart_serializes_only_order_item_identifiers_and_quantities(): void
    {
        $product = $this->product(stock: 4);
        $cart = new Cart;
        $cart->add($product, 2);

        $this->assertSame([
            ['product_id' => $product->id, 'quantity' => 2],
        ], $cart->toOrderItems());
    }

    private function product(int $stock, string $status = 'active'): Product
    {
        $product = new Product([
            'name' => 'Rice',
            'slug' => 'rice',
            'price' => 10,
            'stock' => $stock,
            'status' => $status,
        ]);
        $product->id = random_int(1, 100000);
        $product->thumbnail = null;

        return $product;
    }
}
