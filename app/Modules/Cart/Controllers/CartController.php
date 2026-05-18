<?php

namespace App\Modules\Cart\Controllers;

use App\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly Cart                       $cart,
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function index(): View
    {
        return view('cart::index', [
            'items' => $this->cart->items(),
            'total' => $this->cart->total(),
        ]);
    }

    /**
     * Add a product to the cart.
     * Supports both AJAX (returns JSON) and regular form POST (redirects).
     */
    public function add(Request $request, int $productId): JsonResponse|RedirectResponse
    {
        $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $product  = $this->productRepository->findByIdOrFail($productId);
        $quantity = (int) $request->input('quantity', 1);

        if (! $product->isActive() || ! $product->isInStock()) {
            $message = 'Sản phẩm hiện không có sẵn.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 422)
                : back()->with('error', $message);
        }

        $this->cart->add($product, $quantity);

        if ($request->expectsJson()) {
            return response()->json([
                'message'    => "Đã thêm \"{$product->name}\" vào giỏ hàng.",
                'cart_count' => $this->cart->count(),
                'cart_total' => $this->cart->total(),
            ]);
        }

        return back()->with('success', "Đã thêm \"{$product->name}\" vào giỏ hàng.");
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, int $productId): JsonResponse|RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $this->cart->updateQuantity($productId, (int) $request->input('quantity'));

        if ($request->expectsJson()) {
            return response()->json([
                'cart_count' => $this->cart->count(),
                'cart_total' => $this->cart->total(),
            ]);
        }

        return back()->with('success', 'Giỏ hàng đã được cập nhật.');
    }

    /**
     * Remove a single item from the cart.
     */
    public function remove(int $productId): JsonResponse|RedirectResponse
    {
        $this->cart->remove($productId);

        if (request()->expectsJson()) {
            return response()->json([
                'cart_count' => $this->cart->count(),
                'cart_total' => $this->cart->total(),
            ]);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    /**
     * Empty the entire cart.
     */
    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return back()->with('success', 'Giỏ hàng đã được làm trống.');
    }
}
