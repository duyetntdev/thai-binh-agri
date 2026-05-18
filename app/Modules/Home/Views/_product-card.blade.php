<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden
            hover:shadow-md hover:-translate-y-0.5 transition-all group">

    {{-- Thumbnail --}}
    <a href="{{ route('products.show', $product->slug) }}" class="block relative">
        @if($product->thumbnail)
            <img src="{{ asset('storage/' . $product->thumbnail) }}"
                 alt="{{ $product->name }}"
                 class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-44 bg-green-50 flex items-center justify-center text-5xl">🌿</div>
        @endif

        {{-- Badge --}}
        @if($badge)
            <span class="absolute top-2 left-2 bg-green-600 text-white text-xs font-semibold
                         px-2 py-0.5 rounded-full">
                {{ $badge }}
            </span>
        @endif

        {{-- Out of stock overlay --}}
        @if(! $product->isInStock())
            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                <span class="bg-white text-gray-700 text-xs font-semibold px-3 py-1 rounded-full">
                    Hết hàng
                </span>
            </div>
        @endif
    </a>

    {{-- Info --}}
    <div class="p-3">
        <p class="text-xs text-green-600 font-medium mb-1">{{ $product->category->name }}</p>
        <a href="{{ route('products.show', $product->slug) }}"
           class="text-sm font-semibold text-gray-800 hover:text-green-700 line-clamp-2 leading-snug block mb-2">
            {{ $product->name }}
        </a>

        <div class="flex items-center justify-between">
            <span class="text-green-700 font-bold text-base">
                {{ format_currency($product->price) }}
            </span>
            <span class="text-xs text-gray-400">Đã bán {{ $product->sold_count }}</span>
        </div>

        {{-- Add to cart button (Livewire) --}}
        <div class="mt-3">
            @if($product->isInStock())
                @livewire('add-to-cart', ['product' => $product], key('card-'.$product->id))
            @else
                <button disabled
                        class="w-full text-sm bg-gray-100 text-gray-400 py-2 rounded-lg cursor-not-allowed">
                    Hết hàng
                </button>
            @endif
        </div>
    </div>
</div>
