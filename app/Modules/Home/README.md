# Home

## Mục đích

Cung cấp trang chủ và các nội dung giới thiệu/điều hướng giúp khách hàng khám phá cửa hàng.

## Luồng nghiệp vụ

- Trang chủ hiển thị sản phẩm bán chạy theo `sold_count`, sản phẩm được xem nhiều theo `view_count` và danh mục có sản phẩm đang hoạt động.
- Khách đã đăng nhập có thể thấy các sản phẩm đã mua gần đây; khách vãng lai không có danh sách này.
- Module cũng phục vụ các trang nội dung như giới thiệu, liên hệ, chính sách và tin tức theo routes/views hiện có.

## Quy tắc cần giữ

- Chỉ dùng dữ liệu sản phẩm/danh mục phù hợp với trạng thái hoạt động theo repository hiện có.
- Nội dung cá nhân hóa như lịch sử mua hàng phải giới hạn theo người dùng hiện tại; không hiển thị dữ liệu đó cho khách khác.
- Quy tắc hiển thị chi tiết, lọc và trạng thái sản phẩm thuộc `Products/README.md`.

## Phạm vi code

- `Routes/web.php`, `Controllers/`, `Services/`, `Views/`
- Dữ liệu sản phẩm/danh mục được lấy qua repository dùng chung.
