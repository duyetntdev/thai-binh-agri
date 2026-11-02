# Admin

## Mục đích

Cung cấp khu vực quản trị dành riêng cho tài khoản admin để theo dõi hoạt động cửa hàng và quản lý sản phẩm, đơn hàng, người dùng.

## Luồng nghiệp vụ

- Dashboard tổng hợp doanh thu, số đơn trong ngày, đơn chờ xác nhận, số sản phẩm, sản phẩm sắp hết hàng, số khách hàng và đơn gần đây.
- Admin quản lý vòng đời sản phẩm.
- Admin xem danh sách/chi tiết đơn, chuyển trạng thái đơn và ghi nhận thanh toán.
- Admin tra cứu danh sách và chi tiết người dùng.

## Quy tắc cần giữ

- Tất cả route thuộc khu vực này phải được bảo vệ bởi middleware `auth` và `admin`.
- Trạng thái đơn và điều kiện xác nhận thanh toán phải đi qua logic nghiệp vụ của module Orders; không cập nhật trực tiếp để bỏ qua kiểm tra chuyển trạng thái, thanh toán hoặc tồn kho.
- Blog có giao diện quản trị riêng trong module Blog; khi thay đổi chức năng quản lý bài viết, đọc thêm `app/Modules/Blog/README.md`.

## Phạm vi code

- `Routes/web.php`, `Controllers/`, `Requests/`, `Services/`, `Views/`
- Provider đăng ký routes/views của module

## Liên quan

- `Orders/README.md` — đơn hàng và chuyển trạng thái
- `Products/README.md` — danh mục/sản phẩm hiển thị và tồn kho
- `Blog/README.md` — quản trị bài viết
