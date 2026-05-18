@extends('layouts.app')

@section('title', 'Trang chủ — Nông Sản Thái Bình')

@section('content')

{{-- ===== HERO ===== --}}
<section class="bg-gradient-to-br from-green-700 to-green-500 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Nông Sản Sạch Thái Bình</h1>
        <p class="text-lg text-green-100 mb-8 max-w-xl mx-auto">
            Trực tiếp từ nông dân — tươi ngon, an toàn, giá tốt.
        </p>
        <a href="{{ route('products.index') }}"
           class="inline-block bg-white text-green-700 font-semibold px-8 py-3 rounded-full
                  hover:bg-green-50 transition shadow-md">
            Xem tất cả sản phẩm
        </a>
    </div>
</section>

{{-- ===== CATEGORIES ===== --}}
<section class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
        @foreach($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}"
               class="flex-shrink-0 bg-white border border-gray-200 rounded-full px-5 py-2 text-sm
                      font-medium text-gray-700 hover:border-green-500 hover:text-green-700 transition shadow-sm">
                {{ $category->name }}
                <span class="text-gray-400 ml-1">({{ $category->products_count }})</span>
            </a>
        @endforeach
    </div>
</section>

{{-- ===== SẢN PHẨM BÁN CHẠY ===== --}}
<section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            🔥 Sản phẩm bán chạy
        </h2>
        <a href="{{ route('products.index', ['sort' => 'best_seller']) }}"
           class="text-sm text-green-600 hover:underline font-medium">Xem tất cả →</a>
    </div>

    @if($bestSellers->isEmpty())
        <p class="text-gray-500 text-sm">Chưa có dữ liệu.</p>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($bestSellers as $product)
                @include('home::_product-card', ['product' => $product, 'badge' => 'Bán chạy'])
            @endforeach
        </div>
    @endif
</section>

{{-- ===== SẢN PHẨM YÊU THÍCH (most viewed) ===== --}}
<section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            ❤️ Sản phẩm yêu thích
        </h2>
        <a href="{{ route('products.index', ['sort' => 'most_viewed']) }}"
           class="text-sm text-green-600 hover:underline font-medium">Xem tất cả →</a>
    </div>

    @if($mostViewed->isEmpty())
        <p class="text-gray-500 text-sm">Chưa có dữ liệu.</p>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($mostViewed as $product)
                @include('home::_product-card', ['product' => $product, 'badge' => 'Yêu thích'])
            @endforeach
        </div>
    @endif
</section>

{{-- ===== SẢN PHẨM ĐÃ MUA GẦN ĐÂY (chỉ hiện khi đã login) ===== --}}
@auth
    @if($recentlyPurchased->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    🛒 Bạn đã mua gần đây
                </h2>
                <a href="{{ route('orders.index') }}"
                   class="text-sm text-green-600 hover:underline font-medium">Xem đơn hàng →</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($recentlyPurchased as $product)
                    @include('home::_product-card', ['product' => $product, 'badge' => null])
                @endforeach
            </div>
        </section>
    @endif
@endauth

@endsection
