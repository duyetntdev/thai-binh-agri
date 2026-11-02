# Quy tắc làm việc với code

## Bắt buộc trước khi sửa code

1. Đọc file này trước khi tạo hoặc cập nhật bất kỳ code nào trong working tree.
2. Xác định module và đọc `app/Modules/<Module>/README.md` tương ứng trước khi sửa code của module đó.
3. Nếu thay đổi ảnh hưởng nhiều module, đọc tài liệu của tất cả module liên quan; với thay đổi dùng chung hoặc ngoài `app/Modules`, đọc thêm `README.md` ở gốc và các tài liệu module có liên quan.
4. Nếu module mới chưa có tài liệu, viết tài liệu nghiệp vụ của module trước khi triển khai code.
5. Khi yêu cầu chưa rõ hoặc tài liệu mâu thuẫn với code hiện tại, kiểm tra implementation liên quan và làm rõ nghiệp vụ trước khi tự suy đoán.

## Trong và sau khi sửa code

- Giữ đúng phạm vi nghiệp vụ và các invariant được ghi trong tài liệu module.
- Nếu thay đổi hành vi nghiệp vụ, cập nhật tài liệu module trong cùng thay đổi; không để tài liệu mô tả sai hành vi.
- Tuân theo kiến trúc modular hiện có: Controller xử lý HTTP, Service điều phối nghiệp vụ, Request xác thực input, Repository truy cập dữ liệu; chỉ thay đổi cách phân lớp khi có lý do rõ ràng.
- Ưu tiên enum cho trạng thái, khai báo kiểu trả về cho method và dùng transaction cho thao tác cập nhật nhiều bản ghi.
- Khi xử lý tồn kho hoặc chuyển trạng thái đơn hàng có cạnh tranh đồng thời, giữ cơ chế khóa và kiểm tra điều kiện chuyển trạng thái hiện có.
- Không đưa bí mật, thông tin môi trường hoặc dữ liệu nhạy cảm vào code/tài liệu.
- Chạy kiểm tra phù hợp với phần code đã sửa; không bỏ qua lỗi kiểm tra liên quan.

## Tài liệu module

Tài liệu nghiệp vụ nằm cạnh code tại `app/Modules/<Module>/README.md`. Tài liệu này mô tả mục tiêu, luồng và các quy tắc cần giữ khi sửa module; chi tiết implementation vẫn cần được xác minh trong code liên quan.
