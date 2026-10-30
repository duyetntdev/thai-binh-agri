@extends('layouts.app')

@section('title', $blog->title . ' — Blog nông sản Thái Bình')

@section('content')
<article class="max-w-4xl mx-auto px-4 py-16">
    <a href="{{ route('home.blog.index') }}" class="text-sm font-medium text-green-700 hover:underline">← Tất cả bài viết</a>
    <div class="mt-6 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm sm:p-10">
        <p class="text-sm text-gray-500">{{ $blog->created_at->format('d/m/Y') }}</p>
        <h1 class="mt-3 text-3xl font-bold text-gray-900 sm:text-4xl">{{ $blog->title }}</h1>
        @if($blog->thumbnail)
            <img src="{{ \Illuminate\Support\Str::startsWith($blog->thumbnail, ['http://', 'https://', '/']) ? $blog->thumbnail : \Illuminate\Support\Facades\Storage::disk('public')->url($blog->thumbnail) }}" alt="{{ $blog->title }}" class="mt-8 max-h-[480px] w-full rounded-2xl object-cover" />
        @endif
        @if($blog->excerpt)
            <p class="mt-8 text-lg leading-relaxed text-gray-600">{{ $blog->excerpt }}</p>
        @endif
        <div class="blog-content mt-8">{!! $blog->content !!}</div>
    </div>
</article>
@endsection

@push('styles')
<style>
    .blog-content {
        color: #334155;
        font-size: 1rem;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    .blog-content > * + * {
        margin-top: 1rem;
    }

    .blog-content h1,
    .blog-content h2,
    .blog-content h3,
    .blog-content h4,
    .blog-content h5,
    .blog-content h6 {
        color: #1f2937;
        font-weight: 700;
        line-height: 1.3;
        margin: 1.75rem 0 0.75rem;
    }

    .blog-content h1 { font-size: 2.25rem; }
    .blog-content h2 { font-size: 1.875rem; }
    .blog-content h3 { font-size: 1.5rem; }
    .blog-content h4 { font-size: 1.25rem; }
    .blog-content h5 { font-size: 1.125rem; }
    .blog-content h6 { font-size: 1rem; }

    .blog-content p {
        margin: 0 0 1rem;
    }

    .blog-content a {
        color: #15803d;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .blog-content ul,
    .blog-content ol {
        margin: 1rem 0;
        padding-left: 1.75rem;
    }

    .blog-content ul { list-style: disc; }
    .blog-content ol { list-style: decimal; }
    .blog-content li { margin: 0.35rem 0; }
    .blog-content li > ul { list-style: circle; }

    .blog-content blockquote {
        border-left: 4px solid #16a34a;
        color: #475569;
        font-style: italic;
        margin: 1.5rem 0;
        padding: 0.5rem 0 0.5rem 1rem;
    }

    .blog-content img,
    .blog-content video {
        height: auto;
        max-width: 100%;
    }

    .blog-content figure {
        margin: 1.5rem auto;
        max-width: 100%;
    }

    .blog-content figure.image,
    .blog-content figure.image-style-align-center {
        text-align: center;
    }

    .blog-content figure.image-style-align-left { text-align: left; }
    .blog-content figure.image-style-align-right { text-align: right; }
    .blog-content figcaption { color: #64748b; font-size: 0.875rem; }

    .blog-content table {
        border-collapse: collapse;
        margin: 1.5rem 0;
        max-width: 100%;
        width: 100%;
    }

    .blog-content th,
    .blog-content td {
        border: 1px solid #cbd5e1;
        padding: 0.5rem 0.75rem;
        text-align: left;
    }

    .blog-content th {
        background: #f1f5f9;
        font-weight: 700;
    }

    .blog-content figure.table {
        overflow-x: auto;
    }

    .blog-content hr {
        border: 0;
        border-top: 1px solid #cbd5e1;
        margin: 2rem 0;
    }

    .blog-content pre {
        background: #f1f5f9;
        border-radius: 0.75rem;
        overflow-x: auto;
        padding: 1rem;
    }

    .blog-content code {
        background: #f1f5f9;
        border-radius: 0.25rem;
        padding: 0.1rem 0.3rem;
    }

    .blog-content pre code {
        background: transparent;
        padding: 0;
    }

    .blog-content .text-left { text-align: left; }
    .blog-content .text-center { text-align: center; }
    .blog-content .text-right { text-align: right; }
    .blog-content .text-justify { text-align: justify; }
</style>
@endpush
