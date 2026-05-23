@extends('layouts.app')

@section('title', 'Chỉnh sửa menu')

@section('content')
<section class="max-w-4xl mx-auto px-4 py-10">
    <div class="bg-white rounded-3xl border border-gray-200 p-8 shadow-sm">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Chỉnh sửa menu</h1>

        <form action="{{ route('admin.menu-items.update', $menuItem) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700">Tiêu đề</label>
                <input type="text" name="label" value="{{ old('label', $menuItem->label) }}" required
                       class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700" />
                @error('label')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">URL</label>
                <input type="text" name="url" value="{{ old('url', $menuItem->url) }}" required
                       class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700" />
                @error('url')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Thứ tự</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $menuItem->sort_order) }}" required
                           class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700" />
                    @error('sort_order')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center gap-3 mt-6">
                    <input id="is_active" type="checkbox" name="is_active" {{ $menuItem->is_active ? 'checked' : '' }} class="h-4 w-4 text-green-600 border-gray-300 rounded" />
                    <label for="is_active" class="text-sm text-gray-700">Hiển thị</label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.menu-items.index') }}" class="text-gray-600 hover:text-green-700">Hủy</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700 transition">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</section>
@endsection
