<div>
    @error('stock')
        <p class="text-xs text-red-500 mb-1">{{ $message }}</p>
    @enderror

    @if($added)
        {{-- Success state --}}
        <div class="w-full text-sm bg-green-600 text-white py-2 rounded-lg text-center font-medium
                    flex items-center justify-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Đã thêm vào giỏ
        </div>
    @else
        <div class="flex items-center gap-2">
            {{-- Quantity selector --}}
            <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                <button type="button" wire:click="decrementQuantity"
                        class="px-2 py-1.5 text-gray-500 hover:bg-gray-100 transition text-sm font-bold">−</button>
                <span class="px-3 py-1.5 text-sm font-medium min-w-[2rem] text-center">{{ $quantity }}</span>
                <button type="button" wire:click="incrementQuantity"
                        class="px-2 py-1.5 text-gray-500 hover:bg-gray-100 transition text-sm font-bold">+</button>
            </div>

            {{-- Add button --}}
            <button type="button" wire:click="add"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    class="flex-1 text-sm bg-green-600 text-white py-2 rounded-lg
                           hover:bg-green-700 transition font-medium flex items-center justify-center gap-1">
                <span wire:loading.remove wire:target="add">
                    <svg class="w-4 h-4 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm giỏ
                </span>
                <span wire:loading wire:target="add" class="text-xs">Đang thêm...</span>
            </button>
        </div>

        @error('quantity')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    @endif
</div>
