<div>
    <a href="{{ route('cart.index') }}"
       class="relative flex items-center gap-1 text-gray-600 hover:text-green-700 transition p-1">

        {{-- Cart SVG icon --}}
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                     1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>

        {{-- Badge --}}
        @if($count > 0)
            <span class="absolute -top-1 -right-1 bg-green-600 text-white text-xs font-bold
                         w-5 h-5 rounded-full flex items-center justify-center leading-none">
                {{ $count > 99 ? '99+' : $count }}
            </span>
        @endif
    </a>
</div>
