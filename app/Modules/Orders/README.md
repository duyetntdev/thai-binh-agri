# Orders

## Mục đích

Quản lý vòng đời đơn hàng của khách hàng, từ tạo đơn đến theo dõi hoặc hủy đơn.

## Luồng nghiệp vụ

- Khách đã đăng nhập tạo đơn từ danh sách sản phẩm/số lượng cùng phương thức thanh toán và thông tin giao hàng.
- Khi tạo đơn, hệ thống lấy giá và tồn kho hiện tại từ database, khóa sản phẩm khi kiểm tra tồn, trừ tồn kho, ghi đơn hàng/chi tiết/thanh toán và tăng số đã bán trong cùng transaction.
- Khách chỉ xem đơn của mình; admin có thể xem đơn theo quyền quản trị.
- Khách có thể hủy đơn khi trạng thái cho phép. Hủy đơn khôi phục tồn kho.
- Admin cập nhật trạng thái và ghi nhận thanh toán qua các kiểm tra chuyển trạng thái hiện có.

## Quy tắc cần giữ

- Đơn phải có ít nhất một sản phẩm; số lượng từng sản phẩm phải lớn hơn 0 và không vượt tồn kho.
- Giá trong đơn phải được chụp từ dữ liệu sản phẩm hiện tại, không lấy giá do client hoặc session gửi lên.
- Các thao tác ảnh hưởng nhiều bản ghi (tồn kho, đơn, chi tiết, thanh toán) phải nguyên tử; duy trì khóa hàng để tránh bán vượt tồn.
- Dùng `OrderStatus`, `PaymentStatus` và `PaymentMethod`, không thay bằng magic strings.
- Chuyển trạng thái phải tuân theo `OrderStatus::canTransitionTo`; chỉ giao/hoàn tất khi đã thanh toán theo quy tắc hiện tại.
- Chỉ chủ đơn được hủy; khi hủy phải phục hồi số lượng tồn kho đúng một lần trong cùng transaction.
- Thay đổi checkout/cart cần đọc thêm `Cart/README.md`; thay đổi tích hợp cổng thanh toán cần đọc `Payments/README.md`.

## Phạm vi code

- `Routes/web.php`, `Controllers/`, `Requests/`, `Services/`, `Views/`
- Model, enum và repository đơn hàng/sản phẩm là các phần liên quan trực tiếp.
