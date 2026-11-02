# Blog

## Mục đích

Hiển thị bài viết cho khách truy cập và cho phép admin quản lý nội dung blog.

## Luồng nghiệp vụ

- Khách xem danh sách bài viết đã xuất bản và mở bài viết qua slug.
- Admin tạo, cập nhật, xem danh sách và xóa bài viết qua khu vực quản trị.
- Thumbnail được lưu/xóa qua `BlogThumbnailStorage`; thao tác với database và file cần giữ nhất quán khi tạo, cập nhật hoặc xóa.

## Quy tắc cần giữ

- Nội dung chưa xuất bản không được xuất hiện trên trang public.
- Slug bài viết phải duy nhất; khi cập nhật cần tránh xung đột với chính bài viết đang sửa.
- Khi thay thumbnail, chỉ xóa ảnh cũ sau khi cập nhật thành công; nếu thao tác thất bại, dọn ảnh mới đã tải lên và giữ nguyên dữ liệu cũ.
- Route quản trị phải tiếp tục được bảo vệ bằng `auth` và `admin`.

## Phạm vi code

- `Routes/web.php`, `Controllers/`, `Requests/`, `Services/`, `Views/`
- Bài viết và thumbnail có thể liên quan tới model/repository/storage ngoài module; kiểm tra các phần đó khi sửa luồng lưu trữ.
