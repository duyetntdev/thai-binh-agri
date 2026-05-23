@extends('layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Đơn hàng</h1>
            <p class="text-gray-600 mt-2">Quản lý và xem chi tiết đơn hàng của khách hàng.</p>
        </div>
    </div>

    <form action="{{ route('admin.orders.index') }}" method="GET" class="mt-6 grid gap-3 sm:grid-cols-3">
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm kiếm theo khách hàng"
               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700" />
        <select name="status" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700">
            <option value="">Tất cả trạng thái</option>
            @foreach(\App\Models\OrderStatus::cases() as $statusOption)
                <option value="{{ $statusOption->value }}" {{ ($filters['status'] ?? '') === $statusOption->value ? 'selected' : '' }}>{{ $statusOption->label() }}</option>
            @endforeach
        </select>
        <input name="payment_status" value="{{ $filters['payment_status'] ?? '' }}" placeholder="Trạng thái thanh toán"
               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700" />
    </form>

    <div class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-gray-500">
                <tr>
                    <th class="px-4 py-4">Mã</th>
                    <th class="px-4 py-4">Khách hàng</th>
                    <th class="px-4 py-4">Tổng tiền</th>
                    <th class="px-4 py-4">Trạng thái</th>
                    <th class="px-4 py-4">Thanh toán</th>
                    <th class="px-4 py-4">Ngày</th>
                    <th class="px-4 py-4">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-4 font-medium text-gray-900">#{{ $order->id }}</td>
                        <td class="px-4 py-4 text-gray-600">{{ $order->user->name }}</td>
                        <td class="px-4 py-4 text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                        <td class="px-4 py-4 text-gray-700">{{ ucfirst($order->status) }}</td>
                        <td class="px-4 py-4 text-gray-700">{{ ucfirst($order->payment_status) }}</td>
                        <td class="px-4 py-4 text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-green-600 hover:underline">Xem</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">Chưa có đơn hàng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
</section>
@endsection
