@extends('layouts.app')

@section('title', 'Giỏ hàng — Nông Sản Thái Bình')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold text-gray-800 mb-8">🛒 Giỏ hàng của bạn</h1>

    @if($items->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <div class="text-6xl mb-4">🛒</div>
            <p class="text-lg font-medium mb-4">Giỏ hàng trống</p>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
                Tiếp tục mua sắm
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Cart items --}}
            <div class="lg:col-span-2 space-y-3">
                @foreach($items as $item)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex gap-4 items-center">

                        {{-- Thumbnail --}}
                        <a href="{{ route('products.show', $item->slug) }}" class="flex-shrink-0">
                            @if($item->thumbnail)
                                <img src="{{ asset('storage/' . $item->thumbnail) }}"
                                     alt="{{ $item->name }}"
                                     class="w-20 h-20 object-cover rounded-lg">
                            @else
                                <div class="w-20 h-20 bg-green-50 rounded-lg flex items-center justify-center text-3xl">🌿</div>
                            @endif
                        </a>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $item->slug) }}"
                               class="font-semibold text-gray-800 hover:text-green-700 line-clamp-1">
                                {{ $item->name }}
                            </a>
                            <p class="text-green-700 font-bold mt-1">{{ format_currency($item->price) }}</p>
                        </div>

                        {{-- Quantity controls --}}
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('cart.update', $item->productId) }}" class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                @csrf @method('PATCH')
                                <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}"
                                        class="px-2 py-1.5 text-gray-500 hover:bg-gray-100 text-sm font-bold">−</button>
                                <span class="px-3 py-1.5 text-sm font-medium min-w-[2rem] text-center">{{ $item->quantity }}</span>
                                <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}"
                                        class="px-2 py-1.5 text-gray-500 hover:bg-gray-100 text-sm font-bold">+</button>
                            </form>

                            {{-- Subtotal --}}
                            <span class="text-sm font-semibold text-gray-700 w-24 text-right">
                                {{ format_currency($item->subtotal()) }}
                            </span>

                            {{-- Remove --}}
                            <form method="POST" action="{{ route('cart.remove', $item->productId) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

                {{-- Clear cart --}}
                <form method="POST" action="{{ route('cart.clear') }}" class="text-right">
                    @csrf @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Xóa toàn bộ giỏ hàng?')"
                            class="text-sm text-red-500 hover:underline">
                        Xóa tất cả
                    </button>
                </form>
            </div>

            {{-- Order summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sticky top-24">
                    <h2 class="font-bold text-gray-800 text-lg mb-4">Tóm tắt đơn hàng</h2>

                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <div class="flex justify-between">
                            <span>Tạm tính ({{ $items->sum('quantity') }} sản phẩm)</span>
                            <span>{{ format_currency($total) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Phí vận chuyển</span>
                            <span class="text-green-600 font-medium">Miễn phí</span>
                        </div>
                    </div>

                    <div class="border-t pt-4 flex justify-between font-bold text-gray-800 text-base mb-6">
                        <span>Tổng cộng</span>
                        <span class="text-green-700 text-lg">{{ format_currency($total) }}</span>
                    </div>

                    @auth
                        <a href="{{ route('orders.store') }}"
                           onclick="event.preventDefault(); document.getElementById('checkout-form').submit();"
                           class="block w-full text-center bg-green-600 text-white font-semibold py-3
                                  rounded-lg hover:bg-green-700 transition">
                            Đặt hàng ngay
                        </a>
                        {{-- Hidden form to POST to orders.store --}}
                        <form id="checkout-form" method="POST" action="{{ route('orders.store') }}" class="hidden">
                            @csrf
                            @foreach($items as $item)
                                <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $item->productId }}">
                                <input type="hidden" name="items[{{ $loop->index }}][quantity]" value="{{ $item->quantity }}">
                            @endforeach
                            <input type="hidden" name="payment_method" value="cod">
                            <input type="hidden" name="shipping_address" value="{{ auth()->user()->address ?? '' }}">
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="block w-full text-center bg-green-600 text-white font-semibold py-3
                                  rounded-lg hover:bg-green-700 transition">
                            Đăng nhập để đặt hàng
                        </a>
                    @endauth

                    <a href="{{ route('products.index') }}"
                       class="block text-center text-sm text-gray-500 hover:text-green-700 mt-3">
                        ← Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
