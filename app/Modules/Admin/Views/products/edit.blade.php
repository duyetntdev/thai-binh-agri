@extends('layouts.app')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
<section class="max-w-4xl mx-auto px-4 py-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa sản phẩm</h1>
            <p class="text-gray-600 mt-2">Cập nhật thông tin sản phẩm hiện có.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-green-600 hover:underline">← Quay lại danh sách</a>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-6 rounded-3xl border border-gray-200 bg-white p-8 shadow-sm">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
            <input name="name" value="{{ old('name', $product->name) }}" type="text"
                   class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700" />
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Mô tả</label>
            <textarea name="description" rows="5"
                      class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">{{ old('description', $product->description) }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Giá</label>
                <input name="price" value="{{ old('price', $product->price) }}" type="number" step="0.01"
                       class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700" />
                @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tồn kho</label>
                <input name="stock" value="{{ old('stock', $product->stock) }}" type="number" min="0"
                       class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700" />
                @error('stock')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Danh mục</label>
                <select name="category_id"
                        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700">
                    <option value="">Chọn danh mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select name="status"
                        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700">
                    <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Ngưng hoạt động</option>
                </select>
                @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Hình ảnh</label>
            <input name="thumbnail" value="{{ old('thumbnail', $product->thumbnail) }}" type="text"
                   class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700"
                   placeholder="URL hoặc đường dẫn ảnh" />
            @error('thumbnail')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Hủy</a>
            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700">Lưu thay đổi</button>
        </div>
    </form>
</section>
@endsection
