# Cart

## Mục đích

Cho phép khách hàng gom sản phẩm trước khi đặt hàng.

## Luồng nghiệp vụ

- Giỏ hàng được lưu trong session, dùng được cho cả khách chưa đăng nhập và người đã đăng nhập.
- Mỗi sản phẩm được định danh bằng `product_id`; thêm sản phẩm đang hoạt động/còn hàng sẽ cộng số lượng hiện có.
- Số lượng trong giỏ không vượt quá tồn kho tại thời điểm thêm/cập nhật.
- Người dùng có thể xem giỏ, cập nhật số lượng, xóa một sản phẩm hoặc làm trống giỏ.
- Luồng đặt hàng sử dụng danh sách `product_id` và `quantity`; Orders phải xác minh lại tồn kho và dùng giá hiện tại từ database, không tin giá trong session.

## Quy tắc cần giữ

- Giỏ hàng chỉ là dữ liệu tạm để chuẩn bị đặt hàng, không phải nguồn dữ liệu chính thức cho giá hoặc tồn kho.
- Không cho sản phẩm ngừng bán/hết hàng tiếp tục được thêm vào giỏ; tồn kho có thể thay đổi nên cần kiểm tra lại khi tạo đơn.
- Hỗ trợ cả response JSON cho tương tác AJAX và redirect cho form thông thường ở các thao tác hiện có.
- Thay đổi tích hợp checkout cần đọc thêm `app/Modules/Orders/README.md`.

## Phạm vi code

- HTTP layer: `Controllers/`, `Requests/`, `Routes/`, `Views/`
- Trạng thái và phép tính giỏ hàng nằm trong `app/Cart/`; Livewire có thể cập nhật giao diện giỏ hàng.
