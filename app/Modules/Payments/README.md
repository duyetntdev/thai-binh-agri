# Payments

## Mục đích

Khởi tạo thanh toán và ghi nhận kết quả thanh toán cho đơn hàng qua VNPay hoặc COD.

## Luồng nghiệp vụ

- Chủ đơn khởi tạo thanh toán; đơn đã thanh toán không được yêu cầu thanh toán lại.
- Thanh toán VNPay tạo URL chuyển hướng có chữ ký; callback được kiểm tra chữ ký trước khi ghi nhận kết quả.
- Kết quả thành công cập nhật bản ghi payment và trạng thái thanh toán của order; thất bại đánh dấu payment thất bại.
- COD hiển thị thông tin xác nhận; admin ghi nhận thanh toán COD thông qua luồng quản trị đơn hàng.

## Quy tắc cần giữ

- Chỉ chủ sở hữu đơn được khởi tạo/xem kết quả thanh toán.
- Không xử lý callback VNPay nếu chữ ký không hợp lệ.
- Giữ đúng định dạng số tiền, tham số, cách tạo chữ ký và kiểm tra response theo hợp đồng VNPay.
- Trạng thái payment và order phải được cập nhật nhất quán; không cho callback lặp hoặc request không hợp lệ làm sai trạng thái đơn.
- Không ghi merchant secret hoặc dữ liệu xác thực vào log, response hay tài liệu.
- Khi thay đổi việc chuyển đơn sang giao hàng/hoàn tất, đọc thêm `Orders/README.md`.

## Phạm vi code

- `Routes/web.php`, `Controllers/`, `Services/`, `Views/`
- Payment/order repository và cấu hình VNPay là phụ thuộc trực tiếp.
