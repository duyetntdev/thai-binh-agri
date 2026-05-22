@extends('layouts.app')

@section('title', "{{ $product->name }} — Nông Sản Thái Bình")

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="grid gap-8 lg:grid-cols-[1.3fr_0.9fr]">
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                @if($product->thumbnail)
                    <img src="{{ asset('storage/' . $product->thumbnail) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-[28rem] object-cover">
                @else
                    <div class="w-full h-[28rem] bg-green-50 flex items-center justify-center text-7xl">🌿</div>
                @endif
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $product->name }}</h1>
                    <p class="text-sm text-gray-500 mb-4">Danh mục: <strong>{{ $product->category->name }}</strong></p>
                    <p class="text-sm text-gray-500">{{ $product->description ?? 'Sản phẩm nông sản tươi ngon, đảm bảo chất lượng.' }}</p>
                </div>
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-lg font-semibold text-green-700">{{ format_currency($product->price) }}</span>
                        <span class="text-xs text-gray-400">Đã bán {{ $product->sold_count }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Tình trạng: <span class="font-semibold text-gray-800">{{ $product->isInStock() ? 'Còn hàng' : 'Hết hàng' }}</span></p>
                    <p class="text-sm text-gray-600 mb-6">Lượt xem: {{ $product->view_count }}</p>

                    @if($product->isInStock())
                        @livewire('add-to-cart', ['product' => $product])
                    @else
                        <button disabled class="w-full rounded-xl bg-gray-200 py-3 text-sm font-semibold text-gray-500">
                            Hết hàng
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Sản phẩm liên quan</h2>
                <div class="space-y-4">
                    @forelse($related as $item)
                        <a href="{{ route('products.show', $item->slug) }}"
                           class="block rounded-2xl border border-gray-100 bg-gray-50 p-4 text-sm text-gray-700 hover:border-green-600 hover:bg-white transition">
                            {{ $item->name }}
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">Không có sản phẩm liên quan.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
