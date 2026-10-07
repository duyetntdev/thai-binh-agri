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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

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
        $data = $request->validated();
        $uploads = $data['images'] ?? [];
        unset($data['images']);

        $paths = $this->storeImages($uploads);
        $data['images'] = $paths;
        $data['thumbnail'] = $paths[0] ?? null;

        try {
            $product = $this->productRepository->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredImages($paths);
            throw $exception;
        }

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
        $data = $request->validated();
        $uploads = $data['images'] ?? [];
        unset($data['images']);

        if ($uploads !== []) {
            $oldPaths = array_merge($product->images ?? [], [$product->thumbnail]);
            $paths = $this->storeImages($uploads);
            $data['images'] = $paths;
            $data['thumbnail'] = $paths[0];

            try {
                $this->productRepository->update($product, $data);
            } catch (Throwable $exception) {
                $this->deleteStoredImages($paths);
                throw $exception;
            }

            $this->deleteStoredImages($oldPaths);
        } else {
            $this->productRepository->update($product, $data);
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Sản phẩm đã được cập nhật.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productRepository->delete($product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được xóa.');
    }

    /**
     * @param array<int, UploadedFile> $uploads
     * @return array<int, string>
     */
    private function storeImages(array $uploads): array
    {
        $paths = [];

        try {
            foreach ($uploads as $upload) {
                $path = $upload->store('products', 'public');
                if ($path === false) {
                    throw new \RuntimeException('Không thể lưu hình ảnh sản phẩm.');
                }
                $paths[] = $path;
            }
        } catch (Throwable $exception) {
            $this->deleteStoredImages($paths);
            throw $exception;
        }

        return $paths;
    }

    /**
     * @param array<int, mixed> $paths
     */
    private function deleteStoredImages(array $paths): void
    {
        $paths = array_values(array_unique(array_filter(
            $paths,
            static fn ($path) => is_string($path) && str_starts_with($path, 'products/'),
        )));

        if ($paths !== []) {
            Storage::disk('public')->delete($paths);
        }
    }
}
