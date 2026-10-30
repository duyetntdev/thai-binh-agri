@extends('layouts.app')

@section('title', 'Blog nông sản — Nông Sản Thái Bình')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="mb-10">
        <h1 class="text-4xl font-bold text-gray-900">Blog</h1>
        <p class="mt-3 text-gray-600">Câu chuyện nông sản, mùa vụ và những thông tin hữu ích từ Thái Bình.</p>
    </div>

    @if($blogs->isEmpty())
        <div class="rounded-3xl border border-gray-200 bg-white p-10 text-center text-gray-600">
            Chưa có bài viết nào. Vui lòng quay lại sau.
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($blogs as $blog)
                <article class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                    @if($blog->thumbnail)
                        <a href="{{ route('home.blog.show', $blog->slug) }}">
                            <img src="{{ \Illuminate\Support\Str::startsWith($blog->thumbnail, ['http://', 'https://', '/']) ? $blog->thumbnail : \Illuminate\Support\Facades\Storage::disk('public')->url($blog->thumbnail) }}" alt="{{ $blog->title }}" class="h-52 w-full object-cover" />
                        </a>
                    @endif
                    <div class="p-6">
                        <p class="text-sm text-gray-500">{{ $blog->created_at->format('d/m/Y') }}</p>
                        <h2 class="mt-2 text-xl font-semibold text-gray-900">
                            <a href="{{ route('home.blog.show', $blog->slug) }}" class="hover:text-green-700">{{ $blog->title }}</a>
                        </h2>
                        @if($blog->excerpt)
                            <p class="mt-3 text-gray-600">{{ $blog->excerpt }}</p>
                        @endif
                        <a href="{{ route('home.blog.show', $blog->slug) }}" class="mt-5 inline-block font-medium text-green-700 hover:underline">Đọc bài viết →</a>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $blogs->links() }}</div>
    @endif
</section>
@endsection
