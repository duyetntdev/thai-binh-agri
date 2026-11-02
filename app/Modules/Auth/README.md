# Auth

## Mục đích

Đăng ký, đăng nhập và đăng xuất tài khoản khách hàng.

## Luồng nghiệp vụ

- Đăng ký tạo tài khoản với vai trò `customer`, mã hóa mật khẩu và đăng nhập tài khoản sau khi tạo.
- Đăng nhập xác thực bằng email/mật khẩu, hỗ trợ tùy chọn ghi nhớ và làm mới session sau khi thành công.
- Đăng xuất kết thúc phiên đăng nhập, hủy session và tạo lại CSRF token.

## Quy tắc cần giữ

- Đăng ký công khai chỉ tạo tài khoản khách hàng; không nhận vai trò admin từ input người dùng.
- Mật khẩu phải được băm bằng cơ chế của Laravel, không lưu hoặc ghi log dưới dạng rõ.
- Giữ các biện pháp bảo vệ session hiện có khi thay đổi đăng nhập/đăng xuất.
- Các thao tác đặt hàng và xem đơn hàng cần xác thực người dùng theo quy tắc trong `Orders/README.md`.

## Phạm vi code

- `Routes/web.php`, `Controllers/`, `Requests/`, `Services/`, `Views/`
- Provider đăng ký routes/views của module
