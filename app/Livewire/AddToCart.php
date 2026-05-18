<?php

namespace App\Livewire;

use App\Cart\Cart;
use App\Models\Product;
use Livewire\Component;

/**
 * "Add to cart" button with quantity selector.
 * Dispatches 'cart-updated' so CartIcon refreshes automatically.
 */
class AddToCart extends Component
{
    public Product $product;
    public int     $quantity = 1;
    public bool    $added    = false;

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function add(Cart $cart): void
    {
        if (! $this->product->isActive() || ! $this->product->isInStock()) {
            $this->addError('stock', 'Sản phẩm hiện không có sẵn.');
            return;
        }

        if ($this->quantity < 1 || $this->quantity > $this->product->stock) {
            $this->addError('quantity', "Số lượng không hợp lệ (tối đa {$this->product->stock}).");
            return;
        }

        $cart->add($this->product, $this->quantity);

        $this->added = true;

        // Notify CartIcon to refresh
        $this->dispatch('cart-updated');

        // Reset the "added" flash after 2 seconds via JS
        $this->js("setTimeout(() => \$wire.added = false, 2000)");
    }

    public function incrementQuantity(): void
    {
        if ($this->quantity < $this->product->stock) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.add-to-cart');
    }
}
