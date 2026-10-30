@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<section class="max-w-6xl mx-auto px-4 py-10">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Đơn hàng #{{ $order->id }}</h1>
            <p class="text-gray-600 mt-2">Thông tin chi tiết và trạng thái đơn hàng.</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-green-600 hover:underline">← Quay lại danh sách</a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="mb-8 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Thao tác đơn hàng</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Trạng thái: {{ $order->status->label() }} · Thanh toán: {{ $order->payment_status->label() }}
                </p>
            </div>

            @error('status')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            @error('payment')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

            @if($order->status === \App\Models\OrderStatus::PENDING)
                <div class="flex flex-wrap gap-3">
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ \App\Models\OrderStatus::PROCESSING->value }}">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700">Xác nhận đơn hàng</button>
                    </form>
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST"
                          onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ \App\Models\OrderStatus::CANCELLED->value }}">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-red-300 px-6 py-3 text-sm font-semibold text-red-700 hover:bg-red-50">Hủy đơn hàng</button>
                    </form>
                </div>
            @elseif($order->status === \App\Models\OrderStatus::PROCESSING && ! $order->isPaid())
                <form action="{{ route('admin.orders.mark-paid', $order) }}" method="POST"
                      onsubmit="return confirm('Xác nhận đã nhận đủ tiền thanh toán cho đơn hàng này?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">Đánh dấu đã thanh toán</button>
                </form>
            @elseif($order->status === \App\Models\OrderStatus::PROCESSING && $order->isPaid())
                <div class="flex flex-wrap gap-3">
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ \App\Models\OrderStatus::SHIPPED->value }}">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Đánh dấu đang giao</button>
                    </form>
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ \App\Models\OrderStatus::DELIVERED->value }}">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700">Hoàn thành / Đã giao</button>
                    </form>
                </div>
            @elseif($order->status === \App\Models\OrderStatus::SHIPPED && $order->isPaid())
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ \App\Models\OrderStatus::DELIVERED->value }}">
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700">Hoàn thành / Đã giao</button>
                </form>
            @elseif($order->status === \App\Models\OrderStatus::PROCESSING)
                <p class="text-sm text-gray-500">Có thể cập nhật giao hàng sau khi đơn hàng được thanh toán.</p>
            @else
                <p class="text-sm text-gray-500">Đơn hàng hiện không có thao tác cập nhật.</p>
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Khách hàng</p>
            <p class="mt-3 text-lg font-semibold text-gray-900">{{ $order->user->name }}</p>
            <p class="text-gray-600">{{ $order->user->email }}</p>
            <p class="mt-4 text-sm text-gray-500">Tổng tiền</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }}₫</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Trạng thái đơn hàng</p>
            <p class="mt-3 text-lg font-semibold text-gray-900">{{ ucfirst($order->status->label()) }}</p>
            <p class="mt-4 text-sm text-gray-500">Trạng thái thanh toán</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ ucfirst($order->payment_status->label()) }}</p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Ngày tạo</p>
            <p class="mt-3 text-lg font-semibold text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p class="mt-4 text-sm text-gray-500">Thanh toán</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $order->payment?->method ?? 'Chưa rõ' }}</p>
        </div>
    </div>

    <div class="mt-8 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-gray-900">Mặt hàng</h2>
        <div class="mt-4 space-y-4">
            @foreach($order->items as $item)
                <div class="grid gap-3 rounded-2xl border border-gray-100 bg-slate-50 p-4 sm:grid-cols-4">
                    <div class="sm:col-span-2">
                        <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                        <p class="text-sm text-gray-600">Số lượng: {{ $item->quantity }}</p>
                    </div>
                    <div class="text-gray-700">Đơn giá: {{ number_format($item->price, 0, ',', '.') }}₫</div>
                    <div class="text-gray-900 font-semibold">Tổng: {{ number_format($item->quantity * $item->price, 0, ',', '.') }}₫</div>
                </div>
            @endforeach
        </div>
    </div>

</section>
@endsection
