@extends('layouts.app')

@section('title', 'Tin tức — Nông Sản Thái Bình')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="grid gap-8 lg:grid-cols-2">
        <article class="bg-white rounded-3xl border border-gray-200 p-8 shadow-sm">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Tin tức</h1>
            <h2 class="text-xl font-semibold text-gray-800 mb-3">Cập nhật thị trường nông sản</h2>
            <p class="text-gray-600 leading-relaxed mb-4">
                Theo dõi các thông tin mới nhất về giá cả nông sản, chương trình khuyến mãi và các sản phẩm đặc sắc từ Nông Sản Thái Bình.
            </p>
        </article>
        <article class="bg-white rounded-3xl border border-gray-200 p-8 shadow-sm">
            <h2 class="text-xl font-semibold text-gray-800 mb-3">Tin nổi bật</h2>
            <ul class="space-y-3 text-gray-600">
                <li>• Chương trình ưu đãi mùa vụ mới.</li>
                <li>• Sản phẩm đạt chuẩn an toàn thực phẩm.</li>
                <li>• Cập nhật thông tin nông nghiệp địa phương.</li>
            </ul>
        </article>
    </div>
</section>
@endsection
