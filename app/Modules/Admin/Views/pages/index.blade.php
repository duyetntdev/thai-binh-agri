@extends('layouts.app')

@section('title', 'Quản lý nội dung trang')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Quản lý nội dung trang</h1>
            <p class="text-gray-600 mt-2">Tạo và chỉnh sửa nội dung cho các trang tĩnh như Giới thiệu, Liên hệ, Tin tức, Chính sách.</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center justify-center bg-green-600 text-white px-5 py-3 rounded-lg shadow hover:bg-green-700 transition">Thêm trang mới</a>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-gray-500">
                <tr>
                    <th class="px-4 py-4">Tiêu đề</th>
                    <th class="px-4 py-4">Slug</th>
                    <th class="px-4 py-4">Trạng thái</th>
                    <th class="px-4 py-4">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($pages as $page)
                    <tr>
                        <td class="px-4 py-4 font-medium text-gray-900">{{ $page->title }}</td>
                        <td class="px-4 py-4 text-gray-600">{{ $page->slug }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $page->is_active ? 'Hoạt động' : 'Tạm ẩn' }}</td>
                        <td class="px-4 py-4 space-x-2">
                            <a href="{{ route('admin.pages.edit', $page) }}" class="text-green-600 hover:underline">Chỉnh sửa</a>
                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Xóa trang này?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">Chưa có nội dung trang nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
