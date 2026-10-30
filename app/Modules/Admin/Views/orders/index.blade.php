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

    <form action="{{ route('admin.orders.index') }}" method="GET" class="mt-6 grid gap-3 sm:grid-cols-4">
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
        <button type="submit" class="rounded-2xl bg-green-600 px-5 py-3 text-sm font-semibold text-white hover:bg-green-700">
            Tìm kiếm
        </button>
    </form>

    @if(session('success'))
        <div class="mt-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

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
                        <td class="px-4 py-4 text-gray-700">{{ ucfirst($order->status->label()) }}</td>
                        <td class="px-4 py-4 text-gray-700">{{ ucfirst($order->payment_status->label()) }}</td>
                        <td class="px-4 py-4 text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-green-600 hover:underline">Xem</a>
                                @if($order->status === \App\Models\OrderStatus::PENDING)
                                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ \App\Models\OrderStatus::PROCESSING->value }}">
                                        <button type="submit" class="rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">Xác nhận</button>
                                    </form>
                                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST"
                                          onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ \App\Models\OrderStatus::CANCELLED->value }}">
                                        <button type="submit" class="rounded-lg border border-red-300 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50">Hủy đơn</button>
                                    </form>
                                @elseif($order->status === \App\Models\OrderStatus::PROCESSING && ! $order->isPaid())
                                    <form action="{{ route('admin.orders.mark-paid', $order) }}" method="POST"
                                          onsubmit="return confirm('Xác nhận đã nhận đủ tiền thanh toán cho đơn hàng này?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Đánh dấu đã thanh toán</button>
                                    </form>
                                @elseif($order->status === \App\Models\OrderStatus::PROCESSING && $order->isPaid())
                                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ \App\Models\OrderStatus::SHIPPED->value }}">
                                        <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700">Đang giao</button>
                                    </form>
                                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ \App\Models\OrderStatus::DELIVERED->value }}">
                                        <button type="submit" class="rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">Đã giao</button>
                                    </form>
                                @elseif($order->status === \App\Models\OrderStatus::SHIPPED && $order->isPaid())
                                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ \App\Models\OrderStatus::DELIVERED->value }}">
                                        <button type="submit" class="rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">Đã giao</button>
                                    </form>
                                @endif
                            </div>
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
