<?php

namespace App\Modules\Products\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Modules\Products\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(Request $request): View
    {
        $filters    = $request->only(['category', 'search', 'sort']);
        $products   = $this->productService->list($filters);
        $categories = $this->productService->categories();

        return view('products::index', compact('products', 'categories', 'filters'));
    }

    public function show(Product $product): View
    {
        // Track lượt xem
        $this->productService->trackView($product);

        $related = $this->productService->related($product);

        return view('products::show', compact('product', 'related'));
    }
}
