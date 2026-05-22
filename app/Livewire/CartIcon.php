<?php

namespace App\Livewire;

use App\Cart\Cart;
use Livewire\Component;

/**
 * Header cart icon with item count badge.
 */
class CartIcon extends Component
{
    public int   $count = 0;
    public float $total = 0;

    protected $listeners = [
        'cartUpdated' => 'refresh',
    ];

    public function mount(Cart $cart): void
    {
        $this->count = $cart->count();
        $this->total = $cart->total();
    }

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
