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
    public bool    $compact  = false;

    public function mount(Product $product, bool $compact = false): void
    {
        $this->product = $product;
        $this->compact = $compact;
        \Log::info('AddToCart mounted', [
            'product_id' => $this->product->id ?? null,
            'compact'    => $this->compact,
        ]);
    }

    public function add(): void
    {
        try {
            \Log::info('Attempting to add product to cart', [
                'product_id' => $this->product->id,
                'quantity'   => $this->quantity,
            ]);
            
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
        } catch (\Throwable $e) {
            \Log::error('AddToCart failed', [
                'product_id' => $this->product->id ?? null,
                'quantity'   => $this->quantity,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            $this->addError('general', 'Không thể thêm sản phẩm vào giỏ hàng. Vui lòng thử lại sau.');
            return;
        }
    }

    public function updatedQuantity(): void
    {
        $this->quantity = max(1, min($this->quantity, $this->product->stock));
    }

    public function incrementQuantity(): void
    {
        \Log::info('AddToCart incrementQuantity called', ['product_id' => $this->product->id ?? null, 'quantity' => $this->quantity]);
        if ($this->quantity < $this->product->stock) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        \Log::info('AddToCart decrementQuantity called', ['product_id' => $this->product->id ?? null, 'quantity' => $this->quantity]);
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.add-to-cart');
    }
}
