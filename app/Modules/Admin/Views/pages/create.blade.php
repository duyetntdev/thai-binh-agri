@extends('layouts.app')

@section('title', 'Thêm nội dung trang')

@section('content')
<section class="max-w-4xl mx-auto px-4 py-10">
    <div class="bg-white rounded-3xl border border-gray-200 p-8 shadow-sm">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Thêm nội dung trang</h1>

        <form action="{{ route('admin.pages.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Tiêu đề</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700" />
                @error('title')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}" required
                       class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700" />
                <p class="text-xs text-gray-500 mt-1">Ví dụ: gioi-thieu, lien-he, tin-tuc, chinh-sach</p>
                @error('slug')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tiêu đề SEO (meta title)</label>
                <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                       class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700" />
                @error('meta_title')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nội dung</label>
                <textarea name="content" rows="10"
                          class="mt-2 w-full rounded-3xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700">{{ old('content') }}</textarea>
                @error('content')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3">
                <input id="is_active" type="checkbox" name="is_active" checked class="h-4 w-4 text-green-600 border-gray-300 rounded" />
                <label for="is_active" class="text-sm text-gray-700">Hiển thị</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.pages.index') }}" class="text-gray-600 hover:text-green-700">Hủy</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700 transition">Lưu</button>
            </div>
        </form>
    </div>
</section>
@endsection
