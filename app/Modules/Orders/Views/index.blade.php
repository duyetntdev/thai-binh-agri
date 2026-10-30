@extends('layouts.app')

@section('title', 'Đơn hàng')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-10">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-600">Orders</p>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">Đơn hàng</h1>
            <p class="mt-2 text-gray-600">Quản lý và xem chi tiết đơn hàng của khách hàng.</p>
        </div>
    </div>

    <form action="{{ route('orders.index') }}" method="GET" class="mt-6 grid gap-3 md:grid-cols-4">
        <div class="md:col-span-2">
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Tìm theo mã đơn, tên khách, số điện thoại"
                class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-emerald-500 focus:outline-none"
            />
        </div>

        <select
            name="status"
            class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-emerald-500 focus:outline-none"
        >
            <option value="">Tất cả trạng thái</option>
            @foreach(\App\Models\OrderStatus::cases() as $statusOption)
                <option value="{{ $statusOption->value }}" @selected(request('status') === $statusOption->value)>
                    {{ $statusOption->label() }}
                </option>
            @endforeach
        </select>

        <select
            name="payment_status"
            class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-emerald-500 focus:outline-none"
        >
            <option value="">Tất cả thanh toán</option>
            @foreach(\App\Models\PaymentStatus::cases() as $paymentOption)
                <option value="{{ $paymentOption->value }}" @selected(request('payment_status') === $paymentOption->value)>
                    {{ $paymentOption->label() }}
                </option>
            @endforeach
        </select>

        <div class="md:col-span-4 flex items-center gap-3">
            <button type="submit" class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-medium text-white hover:bg-emerald-700">
                Tìm kiếm
            </button>

            @if(request()->hasAny(['search', 'status', 'payment_status']))
                <a href="{{ route('orders.index') }}" class="rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Xóa lọc
                </a>
            @endif
        </div>
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
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 font-semibold text-gray-900">#{{ $order->id }}</td>
                        <td class="px-4 py-4 text-gray-700">
                            {{ optional($order->user)->name ?? 'Khách đã xóa' }}
                        </td>
                        <td class="px-4 py-4 font-medium text-gray-900">
                            {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}₫
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                {{ $order->status?->label() ?? 'Không xác định' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                {{ $order->payment_status?->label() ?? 'Không xác định' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-gray-500">
                            {{ $order->created_at?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-4">
                            <a href="{{ route('orders.show', $order) }}" class="text-emerald-600 hover:text-emerald-700 hover:underline">
                                Xem
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-2 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17h6M9 13h6M9 9h6M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z" />
                                </svg>
                                <span>Chưa có đơn hàng nào.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="mt-6">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
</section>
@endsection