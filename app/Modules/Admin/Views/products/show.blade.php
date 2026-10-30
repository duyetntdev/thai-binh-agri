@extends('layouts.app')

@section('title', 'Chi tiết sản phẩm')

@section('content')
@php
    $galleryImages = array_map(
        static fn (string $image): string => filter_var($image, FILTER_VALIDATE_URL) ? $image : asset('storage/' . $image),
        $product->galleryImages(),
    );
@endphp

<section class="max-w-4xl mx-auto px-4 py-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="text-gray-600 mt-2">Chi tiết sản phẩm và thông tin tồn kho.</p>
        </div>
        <a href="{{ route('admin.products.edit', $product) }}" class="text-sm text-green-600 hover:underline">Chỉnh sửa</a>
    </div>

    <div class="space-y-6 rounded-3xl border border-gray-200 bg-white p-8 shadow-sm">
        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <p class="text-sm text-gray-500">Danh mục</p>
                <p class="mt-2 text-base font-semibold text-gray-900">{{ $product->category->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Giá</p>
                <p class="mt-2 text-base font-semibold text-gray-900">{{ number_format($product->price, 0, ',', '.') }}₫</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tồn kho</p>
                <p class="mt-2 text-base font-semibold text-gray-900">{{ $product->stock }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Trạng thái</p>
                <p class="mt-2 text-base font-semibold text-gray-900">{{ ucfirst($product->status) }}</p>
            </div>
        </div>

        <div>
            <p class="text-sm text-gray-500">Mô tả</p>
            <p class="mt-3 text-gray-700 leading-7">{{ $product->description }}</p>
        </div>

        @if($galleryImages)
            <div>
                <p class="text-sm text-gray-500">Hình ảnh</p>
                <div x-data="{ activeImage: 0, images: @js($galleryImages) }" class="mt-3">
                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-gray-50">
                        <img :src="images[activeImage]" alt="{{ $product->name }}"
                             class="h-80 w-full object-contain transition-opacity duration-200" />
                    </div>
                    <div class="mt-3 flex gap-3 overflow-x-auto pb-1">
                        @foreach($galleryImages as $index => $image)
                            <button type="button"
                                    @click="activeImage = {{ $index }}"
                                    @mouseenter="activeImage = {{ $index }}"
                                    :aria-pressed="activeImage === {{ $index }}"
                                    aria-label="Xem ảnh {{ $index + 1 }}"
                                    :class="activeImage === {{ $index }} ? 'border-green-600 ring-2 ring-green-100' : 'border-gray-200 hover:border-green-400'"
                                    class="h-20 w-20 shrink-0 overflow-hidden rounded-xl border-2 transition">
                                <img src="{{ $image }}" alt="" class="h-full w-full object-cover" />
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
