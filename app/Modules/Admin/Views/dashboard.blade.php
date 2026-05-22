@extends('layouts.app')

@section('title', 'Bảng điều khiển quản trị')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bảng điều khiển</h1>
            <p class="text-gray-600 mt-2">Tổng quan hệ thống quản trị của Thái Bình Agri.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center bg-green-600 text-white px-5 py-3 rounded-lg shadow hover:bg-green-700 transition">Sản phẩm</a>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center bg-indigo-600 text-white px-5 py-3 rounded-lg shadow hover:bg-indigo-700 transition">Đơn hàng</a>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center bg-slate-600 text-white px-5 py-3 rounded-lg shadow hover:bg-slate-700 transition">Người dùng</a>
        </div>
    </div>

    <div class="grid gap-4 mt-8 md:grid-cols-3 xl:grid-cols-4">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm uppercase tracking-[0.2em] text-gray-500">Tổng doanh thu</p>
            <p class="mt-4 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_revenue'], 0, ',', '.') }}₫</p>
        </div>
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm uppercase tracking-[0.2em] text-gray-500">Đơn hôm nay</p>
            <p class="mt-4 text-3xl font-semibold text-gray-900">{{ $stats['orders_today'] }}</p>
        </div>
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm uppercase tracking-[0.2em] text-gray-500">Đơn đang chờ</p>
            <p class="mt-4 text-3xl font-semibold text-gray-900">{{ $stats['pending_orders'] }}</p>
        </div>
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm uppercase tracking-[0.2em] text-gray-500">Khách hàng</p>
            <p class="mt-4 text-3xl font-semibold text-gray-900">{{ $stats['total_customers'] }}</p>
        </div>
    </div>

    <div class="grid gap-4 mt-8 lg:grid-cols-2">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-semibold text-gray-900">Sản phẩm & tồn kho</h2>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-sm text-gray-500">Tổng sản phẩm</p>
                    <p class="mt-3 text-2xl font-semibold text-gray-900">{{ $stats['total_products'] }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-sm text-gray-500">Sản phẩm sắp hết</p>
                    <p class="mt-3 text-2xl font-semibold text-gray-900">{{ $stats['low_stock'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-semibold text-gray-900">Doanh thu 6 tháng</h2>
            <div class="mt-4 space-y-3">
                @foreach($stats['revenue_by_month'] as $item)
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-4">
                        <span class="text-sm text-gray-600">{{ $item['year'] }}/{{ str_pad($item['month'], 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="font-semibold text-gray-900">{{ number_format($item['total'], 0, ',', '.') }}₫</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm mt-8">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Đơn hàng gần đây</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-green-600 hover:underline">Xem tất cả</a>
        </div>
        @if($stats['recent_orders']->isEmpty())
            <p class="mt-4 text-gray-500">Chưa có đơn hàng.</p>
        @else
            <div class="mt-5 overflow-hidden rounded-3xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Mã đơn</th>
                            <th class="px-4 py-3">Khách hàng</th>
                            <th class="px-4 py-3">Tổng tiền</th>
                            <th class="px-4 py-3">Trạng thái</th>
                            <th class="px-4 py-3">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($stats['recent_orders'] as $order)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">#{{ $order->id }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $order->user->name }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                                <td class="px-4 py-3 text-gray-600">{{ $order->status }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
@endsection
