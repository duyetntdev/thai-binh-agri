@extends('layouts.app')

@section('title', 'Liên hệ — Nông Sản Thái Bình')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="bg-white rounded-3xl border border-gray-200 p-10 shadow-sm">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Liên hệ</h1>
        <p class="text-gray-600 leading-relaxed mb-6">
            Mọi thắc mắc và yêu cầu hỗ trợ xin vui lòng liên hệ với chúng tôi qua các kênh sau.
        </p>
        <div class="grid gap-6 md:grid-cols-2 text-gray-700">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Địa chỉ</h2>
                <p>123 Đường Lý Bôn, TP. Thái Bình</p>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Điện thoại</h2>
                <p>0912 345 678</p>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Email</h2>
                <p>info@thaibinh-agri.vn</p>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Giờ làm việc</h2>
                <p>Thứ 2 - Thứ 7: 8:00 - 18:00</p>
            </div>
        </div>
    </div>
</section>
@endsection
