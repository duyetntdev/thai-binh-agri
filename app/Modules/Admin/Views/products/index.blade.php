@extends('layouts.app')

@section('title', 'Quản lý sản phẩm')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Sản phẩm</h1>
            <p class="text-gray-600 mt-2">Quản lý danh sách sản phẩm trong cửa hàng.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center justify-center bg-green-600 text-white px-5 py-3 rounded-lg shadow hover:bg-green-700 transition">Thêm sản phẩm</a>
    </div>

    <form action="{{ route('admin.products.index') }}" method="GET" class="mt-6 grid gap-3 sm:grid-cols-3">
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm kiếm theo tên"
               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700" />
        <select name="status" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700">
            <option value="">Tất cả trạng thái</option>
            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Hoạt động</option>
            <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Ngưng hoạt động</option>
        </select>
        <select name="category" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700">
            <option value="">Tất cả danh mục</option>
            @foreach($categories as $category)
                <option value="{{ $category->slug }}" {{ ($filters['category'] ?? '') === $category->slug ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </form>

    <div class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-gray-500">
                <tr>
                    <th class="px-4 py-4">Tên</th>
                    <th class="px-4 py-4">Danh mục</th>
                    <th class="px-4 py-4">Giá</th>
                    <th class="px-4 py-4">Tồn kho</th>
                    <th class="px-4 py-4">Trạng thái</th>
                    <th class="px-4 py-4">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($products as $product)
                    <tr>
                        <td class="px-4 py-4 font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-4 py-4 text-gray-600">{{ $product->category->name }}</td>
                        <td class="px-4 py-4 text-gray-900">{{ number_format($product->price, 0, ',', '.') }}₫</td>
                        <td class="px-4 py-4 text-gray-600">{{ $product->stock }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ ucfirst($product->status) }}</td>
                        <td class="px-4 py-4 space-x-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-green-600 hover:underline">Chỉnh sửa</a>
                            <a href="{{ route('admin.products.show', $product) }}" class="text-slate-600 hover:underline">Xem</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Xóa sản phẩm này?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Chưa có sản phẩm nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
</section>
@endsection
