@extends('layouts.app')

@section('title', 'Chi tiết người dùng')

@section('content')
<section class="max-w-5xl mx-auto px-4 py-10">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
            <p class="text-gray-600 mt-2">Thông tin tài khoản và đơn hàng của người dùng.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-sm text-green-600 hover:underline">← Quay lại danh sách</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Email</p>
            <p class="mt-3 text-lg font-semibold text-gray-900">{{ $user->email }}</p>
            <p class="mt-4 text-sm text-gray-500">Số điện thoại</p>
            <p class="mt-1 text-gray-700">{{ $user->phone ?? 'Chưa có' }}</p>
            <p class="mt-4 text-sm text-gray-500">Vai trò</p>
            <p class="mt-1 text-gray-700">{{ ucfirst($user->role->value) }}</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Địa chỉ</p>
            <p class="mt-3 text-gray-700">{{ $user->address ?? 'Chưa có' }}</p>
            <p class="mt-4 text-sm text-gray-500">Thành phố</p>
            <p class="mt-1 text-gray-700">{{ $user->city ?? 'Chưa có' }}</p>
            <p class="mt-4 text-sm text-gray-500">Ngày tạo</p>
            <p class="mt-1 text-gray-700">{{ $user->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="mt-8 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-gray-900">Đơn hàng của người dùng</h2>
        @if($user->orders->isEmpty())
            <p class="mt-4 text-gray-500">Người dùng chưa có đơn hàng nào.</p>
        @else
            <div class="mt-4 overflow-hidden rounded-3xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-gray-500">
                        <tr>
                            <th class="px-4 py-4">Mã đơn</th>
                            <th class="px-4 py-4">Tổng tiền</th>
                            <th class="px-4 py-4">Trạng thái</th>
                            <th class="px-4 py-4">Ngày</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($user->orders as $order)
                            <tr>
                                <td class="px-4 py-4 font-medium text-gray-900">#{{ $order->id }}</td>
                                <td class="px-4 py-4 text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                                <td class="px-4 py-4 text-gray-700">{{ ucfirst($order->status) }}</td>
                                <td class="px-4 py-4 text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
@endsection
