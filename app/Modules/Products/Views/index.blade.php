@extends('layouts.app')

@section('title', 'Sản phẩm — Nông Sản Thái Bình')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-col lg:flex-row gap-6">
        <aside class="lg:w-72 bg-white rounded-3xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Bộ lọc</h2>
            <form action="{{ route('products.index') }}" method="GET" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                    <select name="category" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm">
                        <option value="">Tất cả</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" @selected($filters['category'] ?? '' === $category->slug)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm"
                           placeholder="Tìm sản phẩm...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp</label>
                    <select name="sort" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm">
                        <option value="">Mặc định</option>
                        <option value="best_seller" @selected(($filters['sort'] ?? '') === 'best_seller')>Bán chạy nhất</option>
                        <option value="most_viewed" @selected(($filters['sort'] ?? '') === 'most_viewed')>Yêu thích nhất</option>
                        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Giá tăng dần</option>
                        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Giá giảm dần</option>
                    </select>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-green-600 text-white py-3 text-sm font-semibold hover:bg-green-700 transition">
                    Áp dụng
                </button>
            </form>
        </aside>

        <section class="flex-1 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Sản phẩm</h1>
                    <p class="text-sm text-gray-500 mt-2">Tìm kiếm {{ $products->total() }} sản phẩm phù hợp.</p>
                </div>
                <div class="text-sm text-gray-500">
                    @if($filters['category'] ?? false)
                        Lọc theo: <strong>{{ $categories->firstWhere('slug', $filters['category'])?->name ?? 'Không rõ' }}</strong>
                    @endif
                </div>
            </div>

            @if($products->isEmpty())
                <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-12 text-center">
                    <p class="text-gray-500">Không tìm thấy sản phẩm nào. Vui lòng thử lại với từ khóa khác.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        @include('home::_product-card', ['product' => $product, 'badge' => null])
                    @endforeach
                </div>

                <div class="pt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
