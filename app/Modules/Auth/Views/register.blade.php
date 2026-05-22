@extends('layouts.app')

@section('title', 'Đăng ký — Nông Sản Thái Bình')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-16 px-4 bg-gray-50">
    <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="px-10 py-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Đăng ký</h1>
            <p class="text-sm text-gray-500 mb-8">Tạo tài khoản mới để mua hàng và quản lý đơn hàng.</p>

            @if($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                    <p class="font-semibold mb-2">Có lỗi xảy ra:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <label class="block">
                    <span class="text-sm font-medium text-gray-700">Họ và tên</span>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="mt-2 block w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 focus:border-green-500 focus:ring-2 focus:ring-green-100"
                           placeholder="Nguyễn Văn A" required autofocus>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-gray-700">Email</span>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="mt-2 block w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 focus:border-green-500 focus:ring-2 focus:ring-green-100"
                           placeholder="email@example.com" required>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-gray-700">Mật khẩu</span>
                    <input type="password" name="password"
                           class="mt-2 block w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 focus:border-green-500 focus:ring-2 focus:ring-green-100"
                           placeholder="********" required>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-gray-700">Xác nhận mật khẩu</span>
                    <input type="password" name="password_confirmation"
                           class="mt-2 block w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 focus:border-green-500 focus:ring-2 focus:ring-green-100"
                           placeholder="********" required>
                </label>

                <button type="submit"
                        class="w-full rounded-xl bg-green-600 text-white py-3 text-sm font-semibold hover:bg-green-700 transition">
                    Tạo tài khoản
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Đã có tài khoản?
                <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:text-green-700">Đăng nhập</a>
            </p>
        </div>
    </div>
</div>
@endsection
