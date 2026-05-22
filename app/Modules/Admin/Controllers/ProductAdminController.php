<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Modules\Admin\Requests\StoreProductRequest;
use App\Modules\Admin\Requests\UpdateProductRequest;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductAdminController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'category']);
        $products = $this->productRepository->paginateAll($filters, 20);
        $categories = $this->categoryRepository->all();

        return view('admin::products.index', compact('products', 'categories', 'filters'));
    }

    public function create(): View
    {
        $categories = $this->categoryRepository->all();

        return view('admin::products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productRepository->create($request->validated());

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Sản phẩm đã được tạo thành công.');
    }

    public function show(Product $product): View
    {
        return view('admin::products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = $this->categoryRepository->all();

        return view('admin::products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productRepository->update($product, $request->validated());

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Sản phẩm đã được cập nhật.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productRepository->delete($product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được xóa.');
    }
}
