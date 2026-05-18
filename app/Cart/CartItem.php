<?php

namespace App\Cart;

/**
 * Immutable value object representing a single item in the cart.
 */
final class CartItem
{
    public function __construct(
        public readonly int    $productId,
        public readonly string $name,
        public readonly string $slug,
        public readonly float  $price,
        public readonly int    $quantity,
        public readonly ?string $thumbnail,
    ) {}

    public function subtotal(): float
    {
        return $this->price * $this->quantity;
    }

    public function withQuantity(int $quantity): self
    {
        return new self(
            productId: $this->productId,
            name:      $this->name,
            slug:      $this->slug,
            price:     $this->price,
            quantity:  $quantity,
            thumbnail: $this->thumbnail,
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'price'      => $this->price,
            'quantity'   => $this->quantity,
            'thumbnail'  => $this->thumbnail,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['product_id'],
            name:      $data['name'],
            slug:      $data['slug'],
            price:     (float) $data['price'],
            quantity:  (int) $data['quantity'],
            thumbnail: $data['thumbnail'] ?? null,
        );
    }
}
