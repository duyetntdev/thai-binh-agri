@extends('layouts.app')

@section('title', 'Chỉnh sửa bài viết')

@section('content')
<section class="max-w-4xl mx-auto px-4 py-10">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa bài viết</h1>
        <a href="{{ route('admin.blogs.index') }}" class="text-sm text-green-600 hover:underline">← Quay lại danh sách</a>
    </div>

    <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" class="space-y-6 rounded-3xl border border-gray-200 bg-white p-8 shadow-sm">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                Vui lòng kiểm tra lại thông tin bài viết và các lỗi được đánh dấu bên dưới.
            </div>
        @endif

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Tiêu đề</label>
            <input id="title" name="title" value="{{ old('title', $blog->title) }}" required maxlength="150"
                   class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700" />
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="slug-preview" class="block text-sm font-medium text-gray-700">Đường dẫn bài viết</label>
            <input id="slug-preview" value="{{ $blog->slug }}" readonly
                   class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-700" />
            <p class="mt-1 text-xs text-gray-500">Tự động tạo từ tiêu đề khi lưu.</p>
        </div>

        <div>
            <label for="excerpt" class="block text-sm font-medium text-gray-700">Mô tả ngắn</label>
            <textarea id="excerpt" name="excerpt" rows="3" maxlength="1000"
                      class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">{{ old('excerpt', $blog->excerpt) }}</textarea>
            @error('excerpt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="thumbnail" class="block text-sm font-medium text-gray-700">Ảnh đại diện</label>
            @if($blog->thumbnail)
                <img src="{{ \Illuminate\Support\Str::startsWith($blog->thumbnail, ['http://', 'https://', '/']) ? $blog->thumbnail : \Illuminate\Support\Facades\Storage::disk('public')->url($blog->thumbnail) }}" alt="{{ $blog->title }}" class="mb-3 h-32 rounded-xl object-cover" />
            @endif
            <input id="thumbnail" name="thumbnail" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                   class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700" />
            <p class="mt-1 text-xs text-gray-500">JPG, PNG hoặc WebP; tối đa 5 MB. Để trống nếu muốn giữ ảnh hiện tại.</p>
            @error('thumbnail')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Nội dung HTML</label>
            <textarea id="content" name="content" rows="24" spellcheck="false"
                      class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 font-mono text-sm text-gray-700"
                      placeholder="Dán nội dung HTML của bài viết vào đây">{{ old('content', $blog->content) }}</textarea>
            <p class="mt-1 text-xs text-gray-500">Có thể dán trực tiếp HTML; nội dung sẽ được hiển thị dưới dạng trang trên website.</p>
            @error('content')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-3">
            <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', $blog->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-green-600" />
            <label for="is_active" class="text-sm text-gray-700">Hiển thị trên website</label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.blogs.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Hủy</a>
            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700">Lưu thay đổi</button>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.4/tinymce.min.js" referrerpolicy="no-referrer"></script>
<script>
    const titleInput = document.getElementById('title');
    const slugPreview = document.getElementById('slug-preview');
    const updateSlugPreview = () => {
        slugPreview.value = titleInput.value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
    };

    titleInput.addEventListener('input', updateSlugPreview);

    tinymce.init({
        selector: '#content',
        height: 500,
        menubar: false,
        plugins: 'advlist autolink lists link preview code fullscreen table wordcount',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link table | code',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
    });
</script>
@endpush
