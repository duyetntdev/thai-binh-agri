<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nông Sản Thái Bình')</title>

    {{-- Tailwind CSS CDN (thay bằng Vite build khi production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#16a34a', hover: '#15803d' },
                    }
                }
            }
        }
    </script>

    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

{{-- ===== HEADER ===== --}}
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-green-700">
                <span class="text-2xl">🌾</span>
                <span>Nông Sản Thái Bình</span>
            </a>

            {{-- Navigation --}}
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-green-700 transition">Trang chủ</a>

                <div class="relative group">
                    <button type="button"
                            class="flex items-center gap-1 text-gray-600 hover:text-green-700 transition">
                        Sản phẩm
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-2xl shadow-lg z-20 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <a href="{{ route('products.index') }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-green-700 transition">Tất cả sản phẩm</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        @foreach($headerCategories ?? [] as $cat)
                            <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-green-700 transition">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('home.about') }}" class="text-gray-600 hover:text-green-700 transition">Giới thiệu</a>
                <a href="{{ route('home.contact') }}" class="text-gray-600 hover:text-green-700 transition">Liên hệ</a>
                <a href="{{ route('home.news') }}" class="text-gray-600 hover:text-green-700 transition">Tin tức</a>
                <a href="{{ route('home.policy') }}" class="text-gray-600 hover:text-green-700 transition">Chính sách</a>
            </nav>

            {{-- Right side: Cart + Auth --}}
            <div class="flex items-center gap-4">
                {{-- Cart Icon (Livewire) --}}
                @livewire('cart-icon')

                {{-- Auth --}}
                @auth
                    <div class="relative group">
                        <button class="flex items-center gap-1 text-sm text-gray-600 hover:text-green-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                        </button>
                        <div class="absolute right-0 mt-1 w-44 bg-white rounded-lg shadow-lg border
                                    opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                            <a href="{{ route('orders.index') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Đơn hàng của tôi</a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Quản trị</a>
                            @endif
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm text-gray-600 hover:text-green-700 font-medium">Đăng nhập</a>
                    <a href="{{ route('register') }}"
                       class="text-sm bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        Đăng ký
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>

{{-- Flash messages --}}
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
         class="fixed top-20 right-4 z-50 bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg text-sm">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
         class="fixed top-20 right-4 z-50 bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg text-sm">
        {{ session('error') }}
    </div>
@endif

{{-- ===== MAIN CONTENT ===== --}}
<main>
    @yield('content')
</main>

{{-- ===== FOOTER ===== --}}
<footer class="bg-green-800 text-green-100 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
            <h3 class="font-bold text-white text-lg mb-3">🌾 Nông Sản Thái Bình</h3>
            <p class="text-sm leading-relaxed">
                Cung cấp nông sản sạch, chất lượng cao trực tiếp từ nông dân Thái Bình đến tay người tiêu dùng.
            </p>
        </div>
        <div>
            <h3 class="font-bold text-white mb-3">Liên kết nhanh</h3>
            <ul class="space-y-1 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-white transition">Trang chủ</a></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-white transition">Sản phẩm</a></li>
                <li><a href="{{ route('cart.index') }}" class="hover:text-white transition">Giỏ hàng</a></li>
            </ul>
        </div>
        <div>
            <h3 class="font-bold text-white mb-3">Liên hệ</h3>
            <ul class="space-y-1 text-sm">
                <li>📍 123 Đường Lý Bôn, TP. Thái Bình</li>
                <li>📞 0912 345 678</li>
                <li>✉️ info@thaibinh-agri.vn</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-green-700 text-center py-4 text-xs text-green-300">
        © {{ date('Y') }} Nông Sản Thái Bình. All rights reserved.
    </div>
</footer>

{{-- Zalo / Message / Call widget --}}
<div class="fixed right-5 bottom-5 z-50 flex flex-col items-end gap-3">
    <a href="https://zalo.me/0985626134" target="_blank" rel="noreferrer noopener"
       class="group inline-flex items-center gap-3 rounded-full bg-white/95 px-4 py-3 shadow-lg ring-1 ring-black/5 transition-transform duration-200 hover:-translate-y-0.5" aria-label="Chat Zalo">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#06A3F4] shadow-sm">
            <img src="{{ asset('images/zalo-icon.png') }}" alt="Zalo" class="h-6 w-6" />
        </span>
        <span class="sr-only">Chat Zalo</span>
    </a>
    <a href="https://m.me/0985626134" target="_blank" rel="noreferrer noopener"
       class="group inline-flex items-center gap-3 rounded-full bg-white/95 px-4 py-3 shadow-lg ring-1 ring-black/5 transition-transform duration-200 hover:-translate-y-0.5" aria-label="Nhắn tin qua Facebook">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 shadow-sm">
            <img src="{{ asset('images/message.png') }}" alt="Message" class="h-6 w-6" />
        </span>
        <span class="sr-only">Nhắn tin qua Facebook</span>
    </a>
    <a href="tel:0985626134"
       class="group inline-flex items-center gap-3 rounded-full bg-white/95 px-4 py-3 shadow-lg ring-1 ring-black/5 transition-transform duration-200 hover:-translate-y-0.5" aria-label="Gọi ngay">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-green-700 shadow-sm">
            <img src="{{ asset('images/call.jpg') }}" alt="Gọi" class="h-6 w-6 rounded-full object-cover" />
        </span>
        <span class="sr-only">Gọi ngay</span>
    </a>
</div>

{{-- Alpine.js for dropdown/flash --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@livewireScripts

@stack('scripts')
</body>
</html>
