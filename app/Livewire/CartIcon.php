<?php

namespace App\Livewire;

use App\Cart\Cart;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Header cart icon with item count badge.
 * Listens to the 'cart-updated' browser event dispatched after add/remove.
 */
class CartIcon extends Component
{
    public int   $count = 0;
    public float $total = 0;

    public function mount(Cart $cart): void
    {
        $this->count = $cart->count();
        $this->total = $cart->total();
    }

    #[On('cart-updated')]
    public function refresh(Cart $cart): void
    {
        $this->count = $cart->count();
        $this->total = $cart->total();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.cart-icon');
    }
}
