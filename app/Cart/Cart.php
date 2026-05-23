<?php

namespace App\Cart;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 * Session-backed shopping cart.
 *
 * Items are keyed by product_id in the session so they survive page reloads
 * for both guests and authenticated users.
 */
class Cart
{
    private const SESSION_KEY = 'cart';

    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    /** @return Collection<int, CartItem> */
    public function items(): Collection
    {
        return collect(Session::get(self::SESSION_KEY, []))
            ->map(fn (array $data) => CartItem::fromArray($data));
    }

    public function count(): int
    {
        return $this->items()->sum('quantity');
    }

    public function total(): float
    {
        return $this->items()->sum(fn (CartItem $item) => $item->subtotal());
    }

    public function isEmpty(): bool
    {
        return $this->items()->isEmpty();
    }

    public function has(int $productId): bool
    {
        return $this->items()->has($productId);
    }

    public function get(int $productId): ?CartItem
    {
        return $this->items()->get($productId);
    }

    // -------------------------------------------------------------------------
    // Write
    // -------------------------------------------------------------------------

    /**
     * Add a product to the cart or increase its quantity.
     */
    public function add(Product $product, int $quantity = 1): void
    {
        if ($quantity <= 0) {
            return;
        }

        $items = Session::get(self::SESSION_KEY, []);
        $currentQuantity = isset($items[$product->id])
            ? (int) $items[$product->id]['quantity']
            : 0;

        $newQuantity = min($currentQuantity + $quantity, $product->stock);

        $items[$product->id] = (new CartItem(
            productId: $product->id,
            name:      $product->name,
            slug:      $product->slug,
            price:     (float) $product->price,
            quantity:  $newQuantity,
            thumbnail: $product->thumbnail,
        ))->toArray();

        Session::put(self::SESSION_KEY, $items);
    }

    /**
     * Set the quantity of a cart item directly.
     */
    public function updateQuantity(int $productId, int $quantity): void
    {
        $items = Session::get(self::SESSION_KEY, []);

        if (! isset($items[$productId])) {
            return;
        }

        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }

        $items[$productId]['quantity'] = $quantity;
        Session::put(self::SESSION_KEY, $items);
    }

    /**
     * Remove a single item from the cart.
     */
    public function remove(int $productId): void
    {
        $items = Session::get(self::SESSION_KEY, []);
        unset($items[$productId]);
        Session::put(self::SESSION_KEY, $items);
    }

    /**
     * Empty the entire cart.
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Convert cart items to the format expected by OrderService::create().
     *
     * @return array<int, array{product_id: int, quantity: int}>
     */
    public function toOrderItems(): array
    {
        return $this->items()
            ->map(fn (CartItem $item) => [
                'product_id' => $item->productId,
                'quantity'   => $item->quantity,
            ])
            ->values()
            ->all();
    }
}
