@extends('layouts.app')

@section('title', $page->meta_title ?? ($page->title . ' — Nông Sản Thái Bình'))

@section('content')
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-10">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">{{ $page->title }}</h1>
        <div class="prose prose-slate max-w-none text-gray-700">
            {!! $page->content !!}
        </div>
    </div>
</section>
@endsection
