# Products

## Mục đích

Giúp khách hàng duyệt, tìm kiếm và xem chi tiết các sản phẩm nông sản.

## Luồng nghiệp vụ

- Danh sách sản phẩm hỗ trợ phân trang, lọc theo danh mục/từ khóa và sắp xếp.
- Trang chi tiết tra cứu sản phẩm đang hoạt động bằng slug, ghi nhận lượt xem và hiển thị sản phẩm liên quan cùng danh mục.
- Danh mục hiển thị số lượng sản phẩm đang hoạt động.
- Tạo/sửa sản phẩm trong khu vực quản trị thuộc module Admin.

## Quy tắc cần giữ

- Trang public không hiển thị sản phẩm không hoạt động.
- Slug phải được quản lý theo cơ chế hiện có (`HasSlug`); đường dẫn chi tiết dựa trên slug.
- Giá, trạng thái hoạt động và tồn kho phải lấy từ dữ liệu server; không tin dữ liệu do client gửi khi thêm giỏ hoặc đặt hàng.
- Sửa quản lý sản phẩm cần đọc thêm `Admin/README.md`; sửa giỏ/đặt hàng cần đọc `Cart/README.md` và `Orders/README.md`.

## Phạm vi code

- `Routes/web.php`, `Controllers/`, `Services/`, `Views/`
- Model, repository và module Admin cùng tham gia vào vòng đời sản phẩm.
