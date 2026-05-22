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

    public function add(): void
    {
        if (! $this->product->isActive() || ! $this->product->isInStock()) {
            $this->addError('stock', 'Sản phẩm hiện không có sẵn.');
            return;
        }

        if ($this->quantity < 1 || $this->quantity > $this->product->stock) {
            $this->addError('quantity', "Số lượng không hợp lệ (tối đa {$this->product->stock}).");
            return;
        }

        app(Cart::class)->add($this->product, $this->quantity);

        $this->added = true;

        // Notify CartIcon to refresh
        $this->emit('cartUpdated');
        $this->dispatchBrowserEvent('cart-added');
    }

    public function updatedQuantity(): void
    {
        $this->quantity = max(1, min($this->quantity, $this->product->stock));
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
