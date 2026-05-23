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
            <p class="mt-3 text-lg font-semibold text-gray-900">{{ ucfirst($order->status) }}</p>
            <p class="mt-4 text-sm text-gray-500">Trạng thái thanh toán</p>
            <p class="mt-1 text-lg font-semibold text-gray-900">{{ ucfirst($order->payment_status) }}</p>
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

    <div class="mt-8 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-gray-900">Cập nhật trạng thái</h2>
        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="mt-4 grid gap-4 sm:grid-cols-2">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700">Trạng thái mới</label>
                <select name="status" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700">
                    @foreach(\App\Models\OrderStatus::cases() as $statusOption)
                        <option value="{{ $statusOption->value }}" {{ $order->status === $statusOption->value ? 'selected' : '' }}>{{ $statusOption->label() }}</option>
                    @endforeach
                </select>
                @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-end justify-end gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700">Cập nhật trạng thái</button>
            </div>
        </form>
    </div>
</section>
@endsection
