@extends('layouts.app')

@section('title', 'Quản lý người dùng')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Người dùng</h1>
            <p class="text-gray-600 mt-2">Danh sách tài khoản người dùng và trạng thái.</p>
        </div>
    </div>

    <form action="{{ route('admin.users.index') }}" method="GET" class="mt-6 grid gap-3 sm:grid-cols-3">
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm kiếm tên hoặc email"
               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700" />
        <select name="role" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700">
            <option value="">Tất cả vai trò</option>
            @foreach($roles as $roleOption)
                <option value="{{ $roleOption->value }}" {{ ($filters['role'] ?? '') === $roleOption->value ? 'selected' : '' }}>{{ ucfirst($roleOption->value) }}</option>
            @endforeach
        </select>
        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-5 py-3 text-sm font-semibold text-white hover:bg-green-700">Lọc</button>
    </form>

    <div class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-gray-500">
                <tr>
                    <th class="px-4 py-4">Tên</th>
                    <th class="px-4 py-4">Email</th>
                    <th class="px-4 py-4">Vai trò</th>
                    <th class="px-4 py-4">Ngày tạo</th>
                    <th class="px-4 py-4">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-4 py-4 text-gray-600">{{ $user->email }}</td>
                        <td class="px-4 py-4 text-gray-700">{{ ucfirst($user->role->value) }}</td>
                        <td class="px-4 py-4 text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-4">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-green-600 hover:underline">Xem</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Chưa có người dùng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
</section>
@endsection
