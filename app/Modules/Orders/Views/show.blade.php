@extends('layouts.app')

@section('title', 'Đơn hàng #' . $order->id)

@section('content')
<section class="mx-auto max-w-5xl px-4 py-10">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Đơn hàng #{{ $order->id }}</h1>
            <p class="mt-1 text-sm text-gray-500">Đặt ngày {{ $order->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-sm font-medium text-green-700 hover:underline">
            Quay lại danh sách đơn hàng
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <h2 class="text-sm font-medium text-gray-500">Trạng thái đơn hàng</h2>
            <p class="mt-2 text-lg font-semibold text-gray-900">{{ $order->status?->label() ?? 'Không xác định' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <h2 class="text-sm font-medium text-gray-500">Thanh toán</h2>
            <p class="mt-2 text-lg font-semibold text-gray-900">{{ $order->payment_status?->label() ?? 'Không xác định' }}</p>
            @if($order->payment?->method)
                <p class="mt-1 text-sm text-gray-500">{{ $order->payment->method->label() }}</p>
            @endif
        </div>
    </div>

    <div class="mt-4 rounded-lg border border-gray-200 bg-white p-5">
        <h2 class="text-sm font-medium text-gray-500">Địa chỉ giao hàng</h2>
        @if($order->shipping_address || $order->ward || $order->province)
            <p class="mt-2 text-gray-900">
                {{ collect([$order->shipping_address, $order->ward?->name, $order->province?->name])->filter()->join(', ') }}
            </p>
        @else
            <p class="mt-2 text-sm text-gray-500">Chưa có địa chỉ giao hàng.</p>
        @endif
        @if($order->shipping_phone)
            <p class="mt-2 text-sm text-gray-700">Điện thoại: {{ $order->shipping_phone }}</p>
        @endif
        @if($order->notes)
            <div class="mt-4 border-t border-gray-100 pt-3">
                <h3 class="text-sm font-medium text-gray-500">Ghi chú giao hàng</h3>
                <p class="mt-1 whitespace-pre-line text-gray-900">{{ $order->notes }}</p>
            </div>
        @endif
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-5 py-4">
            <h2 class="font-semibold text-gray-900">Sản phẩm đã đặt</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($order->items as $item)
                <div class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-medium text-gray-900">{{ $item->product?->name ?? 'Sản phẩm không còn khả dụng' }}</p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ number_format((float) $item->price, 0, ',', '.') }}₫ × {{ $item->quantity }}
                        </p>
                    </div>
                    <p class="font-semibold text-gray-900">
                        {{ number_format($item->subtotal(), 0, ',', '.') }}₫
                    </p>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-gray-500">Đơn hàng chưa có sản phẩm.</p>
            @endforelse
        </div>
        <div class="flex items-center justify-between border-t border-gray-200 px-5 py-4">
            <span class="font-semibold text-gray-700">Tổng cộng</span>
            <span class="text-lg font-bold text-green-700">{{ number_format((float) $order->total_amount, 0, ',', '.') }}₫</span>
        </div>
    </div>

    @if($order->canBeCancelled())
        <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-5 text-right"
              onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
            @csrf
            @method('PATCH')
            <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                Hủy đơn hàng
            </button>
        </form>
    @endif
</section>
@endsection