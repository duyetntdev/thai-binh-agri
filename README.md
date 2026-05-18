# 🌾 Nông Sản Thái Bình — E-Commerce Platform

Nền tảng thương mại điện tử bán nông sản sạch đặc sản tỉnh Thái Bình, kết nối trực tiếp nông dân với người tiêu dùng.

---

## Mục lục

- [Mô tả business](#-mô-tả-business)
- [Tech stack](#-tech-stack)
- [Kiến trúc hệ thống](#-kiến-trúc-hệ-thống)
- [Cấu trúc thư mục](#-cấu-trúc-thư-mục)
- [Database schema](#-database-schema)
- [Luồng nghiệp vụ](#-luồng-nghiệp-vụ)
- [Cài đặt với Docker](#-cài-đặt-với-docker)
- [Cài đặt thủ công](#-cài-đặt-thủ-công)
- [Tài khoản mặc định](#-tài-khoản-mặc-định)
- [Routes](#-routes)
- [Biến môi trường](#-biến-môi-trường)

---

## 📋 Mô tả Business

### Bối cảnh

Thái Bình là tỉnh nông nghiệp trọng điểm vùng đồng bằng sông Hồng, nổi tiếng với các đặc sản như **Gạo Tám Xoan**, **Tôm Sú**, **Gà Ta Thả Vườn**. Tuy nhiên, chuỗi phân phối truyền thống qua nhiều tầng trung gian khiến giá thành cao, nông dân lợi nhuận thấp, người tiêu dùng khó tiếp cận sản phẩm sạch có nguồn gốc rõ ràng.

### Giải pháp

Website **Nông Sản Thái Bình** là nền tảng B2C kết nối trực tiếp:

```
Nông dân / HTX Thái Bình  →  Nền tảng  →  Người tiêu dùng toàn quốc
```

### Đối tượng người dùng

| Vai trò | Mô tả |
|---|---|
| **Khách hàng (Customer)** | Người tiêu dùng mua nông sản online, thanh toán qua VNPay hoặc COD |
| **Quản trị viên (Admin)** | Quản lý sản phẩm, danh mục, đơn hàng, người dùng và xem báo cáo doanh thu |

### Tính năng chính

**Khách hàng:**
- Duyệt sản phẩm theo danh mục, tìm kiếm, lọc và sắp xếp
- Trang chủ hiển thị **Sản phẩm bán chạy**, **Sản phẩm yêu thích**, **Đã mua gần đây**
- Thêm sản phẩm vào giỏ hàng (real-time, không reload trang — Livewire)
- Đặt hàng và thanh toán qua **VNPay** hoặc **COD**
- Theo dõi trạng thái đơn hàng, hủy đơn khi chưa xử lý

**Quản trị viên:**
- Dashboard tổng quan: doanh thu, đơn hàng hôm nay, sản phẩm sắp hết hàng
- Quản lý CRUD sản phẩm và danh mục
- Cập nhật trạng thái đơn hàng (pending → processing → shipped → delivered)
- Quản lý danh sách người dùng

---

## 🛠 Tech Stack

| Thành phần | Công nghệ | Phiên bản |
|---|---|---|
| Backend framework | Laravel | 11.x |
| PHP | PHP | 8.2+ |
| Frontend reactive | Livewire | 3.x |
| Database | MySQL | 8.0 |
| Cache / Session | Redis | 7.x |
| Web server | Nginx | 1.25 |
| Container | Docker + Docker Compose | — |
| Thanh toán | VNPay | Sandbox/Production |
| Auth | Laravel Sanctum | 4.x |

---

## 🏗 Kiến trúc hệ thống

### Tổng quan

Dự án áp dụng **Modular Architecture** kết hợp **Repository Pattern** và **Service Layer**:

```
HTTP Request
    │
    ▼
┌─────────────┐
│  Controller │  ← Nhận request, validate input, trả response
└──────┬──────┘
       │ gọi
       ▼
┌─────────────┐
│   Service   │  ← Business logic, orchestration, transactions
└──────┬──────┘
       │ gọi
       ▼
┌──────────────────────┐
│ Repository Interface │  ← Contract (không phụ thuộc implementation)
└──────────┬───────────┘
           │ bind bởi RepositoryServiceProvider
           ▼
┌──────────────────────┐
│ Eloquent Repository  │  ← Truy vấn database thực tế
└──────────┬───────────┘
           │
           ▼
┌──────────┐
│  Model   │  ← Eloquent ORM, relations, scopes
└──────────┘
```

### Dependency Injection Flow

```
RepositoryServiceProvider
    binds: ProductRepositoryInterface → ProductRepository
    binds: OrderRepositoryInterface   → OrderRepository
    binds: CategoryRepositoryInterface → CategoryRepository
    binds: UserRepositoryInterface    → UserRepository
    binds: PaymentRepositoryInterface → PaymentRepository
    singleton: Cart
```

Để swap implementation (ví dụ: test với in-memory repository), chỉ cần thay đổi 1 dòng trong `RepositoryServiceProvider` — không cần sửa Service hay Controller.

### Modular Structure

Mỗi module là một **bounded context** độc lập, tự đăng ký routes và views qua ServiceProvider:

```
Module
├── Controllers/   ← HTTP layer
├── Services/      ← Business logic
├── Requests/      ← Form validation
├── Routes/        ← web.php của module
├── Views/         ← Blade templates
└── Providers/     ← ServiceProvider đăng ký module
```

### Livewire Components

| Component | Mô tả |
|---|---|
| `CartIcon` | Icon giỏ hàng trên header, hiển thị số lượng real-time |
| `AddToCart` | Nút thêm giỏ hàng + chọn số lượng, không reload trang |

Hai component giao tiếp qua browser event `cart-updated`: khi `AddToCart` dispatch event, `CartIcon` tự refresh số lượng.

### Cart Architecture

Giỏ hàng lưu trong **PHP Session** (không cần database), hoạt động cho cả guest và user đã đăng nhập:

```
Cart (singleton)
├── CartItem (value object, immutable)
└── Session key: "cart" → [ product_id => CartItem::toArray() ]
```

---

## 📁 Cấu trúc thư mục

```
thai-binh-agri/
│
├── app/
│   ├── Cart/                          # Giỏ hàng (session-based)
│   │   ├── Cart.php                   # Cart service — add/remove/update
│   │   └── CartItem.php               # Value object bất biến
│   │
│   ├── Exceptions/
│   │   └── InsufficientStockException.php
│   │
│   ├── Helpers/
│   │   └── helpers.php                # format_currency(), format_weight()
│   │
│   ├── Http/Middleware/
│   │   └── EnsureUserIsAdmin.php      # Middleware bảo vệ route /admin
│   │
│   ├── Livewire/
│   │   ├── AddToCart.php              # Nút thêm giỏ hàng
│   │   └── CartIcon.php               # Icon giỏ hàng trên header
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── UserRole.php               # Enum: admin | customer
│   │   ├── Category.php
│   │   ├── Product.php                # Scopes: active(), inStock(), bestSeller()
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── OrderStatus.php            # Enum: pending|processing|shipped|delivered|cancelled
│   │   ├── Payment.php
│   │   ├── PaymentStatus.php          # Enum: pending|completed|failed|refunded
│   │   └── PaymentMethod.php          # Enum: vnpay|cod|bank_transfer
│   │
│   ├── Modules/
│   │   ├── Home/                      # Trang chủ
│   │   ├── Auth/                      # Đăng nhập / Đăng ký
│   │   ├── Cart/                      # Giỏ hàng (HTTP layer)
│   │   ├── Products/                  # Danh sách & chi tiết sản phẩm
│   │   ├── Orders/                    # Đặt hàng & quản lý đơn
│   │   ├── Payments/                  # Thanh toán VNPay / COD
│   │   └── Admin/                     # Quản trị (dashboard, CRUD)
│   │
│   ├── Providers/
│   │   └── RepositoryServiceProvider.php  # Bind interfaces + View Composer
│   │
│   ├── Repositories/
│   │   ├── Contracts/                 # Interfaces (contracts)
│   │   │   ├── BaseRepositoryInterface.php
│   │   │   ├── ProductRepositoryInterface.php
│   │   │   ├── CategoryRepositoryInterface.php
│   │   │   ├── OrderRepositoryInterface.php
│   │   │   ├── UserRepositoryInterface.php
│   │   │   └── PaymentRepositoryInterface.php
│   │   └── Eloquent/                  # Implementations
│   │       ├── BaseRepository.php
│   │       ├── ProductRepository.php
│   │       ├── CategoryRepository.php
│   │       ├── OrderRepository.php
│   │       ├── UserRepository.php
│   │       └── PaymentRepository.php
│   │
│   └── Traits/
│       └── HasSlug.php                # Auto-generate slug từ name
│
├── bootstrap/
│   └── app.php                        # Đăng ký providers, routes, middleware
│
├── database/
│   ├── migrations/                    # 7 migration files
│   └── seeders/                       # User, Category, Product, Order seeders
│
├── docker/
│   ├── nginx/default.conf             # Nginx config
│   └── php/
│       ├── Dockerfile                 # PHP 8.2-fpm-alpine
│       ├── php.ini                    # PHP config
│       └── entrypoint.sh             # Auto migrate + seed khi start
│
├── resources/views/
│   ├── layouts/app.blade.php          # Layout chính (header, footer)
│   └── livewire/                      # Livewire component views
│
├── routes/
│   ├── web.php                        # Entry point (module routes load qua Provider)
│   ├── api.php
│   └── console.php
│
├── docker-compose.yml
├── .env.docker                        # Env cho Docker
├── .dockerignore
├── Makefile                           # Shortcuts: make up, make shell, ...
└── README.md
```

---

## 🗄 Database Schema

### ERD (Entity Relationship)

```
users
 ├── id, name, email, password, phone, address, city
 └── role: enum(admin, customer)
      │
      └──< orders
              ├── id, user_id, total_amount
              ├── status: enum(pending|processing|shipped|delivered|cancelled)
              ├── payment_status: enum(pending|completed|failed|refunded)
              │
              ├──< order_items
              │       ├── id, order_id, product_id, quantity, price
              │       └──> products
              │               ├── id, name, slug, description, price
              │               ├── stock, view_count, sold_count
              │               ├── thumbnail, status: enum(active|inactive)
              │               └──> categories
              │                       └── id, name, slug, description
              │
              └──< payments (1-1)
                      ├── id, order_id, amount
                      ├── method: enum(vnpay|cod|bank_transfer)
                      ├── transaction_id, paid_at
                      └── status: enum(pending|completed|failed|refunded)
```

### Migrations

| File | Bảng |
|---|---|
| `000001_create_users_table` | `users` |
| `000002_create_categories_table` | `categories` |
| `000003_create_products_table` | `products` |
| `000004_create_orders_table` | `orders` |
| `000005_create_order_items_table` | `order_items` |
| `000006_create_payments_table` | `payments` |
| `000007_add_slug_and_view_count_to_products` | Thêm `slug`, `view_count`, `sold_count` vào `products` |

---

## 🔄 Luồng nghiệp vụ

### Luồng mua hàng

```
1. Khách duyệt sản phẩm  →  GET /san-pham
2. Xem chi tiết          →  GET /san-pham/{slug}  (tăng view_count)
3. Thêm giỏ hàng         →  Livewire AddToCart (lưu session)
4. Xem giỏ hàng          →  GET /gio-hang
5. Đặt hàng              →  POST /don-hang  (giảm stock, tăng sold_count)
6. Thanh toán VNPay      →  GET /thanh-toan/don-hang/{id}  →  redirect VNPay
7. Callback VNPay        →  GET /thanh-toan/callback  (verify signature)
8. Kết quả               →  GET /thanh-toan/ket-qua/{id}
```

### Luồng hủy đơn

```
Khách hủy  →  PATCH /don-hang/{id}/cancel
           →  Kiểm tra status (chỉ pending/processing mới được hủy)
           →  Hoàn stock về sản phẩm (DB transaction)
           →  Cập nhật status = cancelled
```

### Luồng thanh toán VNPay

```
App  →  buildPaymentUrl(order, ip)  →  VNPay sandbox
                                            │
                                            └─ redirect về /thanh-toan/callback
                                                    │
                                                    ├─ verifyCallback (HMAC-SHA512)
                                                    ├─ isSuccessful (ResponseCode == '00')
                                                    └─ markCompleted / markFailed
```

---

## 🐳 Cài đặt với Docker

### Yêu cầu

- Docker Engine 24+
- Docker Compose v2+

### Các bước

**1. Clone project**
```bash
git clone <repo-url> thai-binh-agri
cd thai-binh-agri
```

**2. Khởi động (một lệnh duy nhất)**
```bash
make up
# hoặc: docker compose up -d --build
```

Container sẽ tự động:
- Build PHP image (PHP 8.2 + extensions)
- Chờ MySQL sẵn sàng
- Copy `.env.docker` → `.env`
- Generate `APP_KEY`
- Chạy `migrate`
- Chạy `db:seed` (nếu DB trống)
- Tạo `storage:link`

**3. Truy cập**

| URL | Mô tả |
|---|---|
| http://localhost:8000 | Website chính |
| http://localhost:8000/admin | Trang quản trị |
| http://localhost:8080 | phpMyAdmin |

### Các lệnh Docker thường dùng

```bash
make up               # Khởi động tất cả containers
make down             # Dừng tất cả containers
make shell            # Vào bash trong PHP container
make logs             # Xem log realtime
make logs-php         # Xem log PHP
make fresh            # Reset DB + seed lại (⚠ xóa toàn bộ data)
make migrate          # Chạy migration
make seed             # Chạy seeder
make cache-clear      # Xóa cache Laravel
make ps               # Xem trạng thái containers

# Chạy artisan command bất kỳ
make artisan CMD="route:list"
make artisan CMD="tinker"
```

### Cổng dịch vụ

| Service | Cổng host | Cổng container |
|---|---|---|
| Nginx (App) | 8000 | 80 |
| MySQL | 3307 | 3306 |
| Redis | 6380 | 6379 |
| phpMyAdmin | 8080 | 80 |

> MySQL dùng cổng 3307 và Redis dùng 6380 để tránh conflict với service đang chạy trên máy host.

---

## 💻 Cài đặt thủ công

### Yêu cầu

- PHP 8.2+ với extensions: `pdo_mysql`, `mbstring`, `gd`, `zip`, `redis`, `intl`, `bcmath`
- Composer 2.x
- MySQL 8.0+
- Redis 7+
- Node.js 18+ (nếu build assets)

### Các bước

**1. Clone và cài dependencies**
```bash
git clone <repo-url> thai-binh-agri
cd thai-binh-agri
composer install
```

**2. Cấu hình môi trường**
```bash
cp .env.example .env
php artisan key:generate
```

Chỉnh sửa `.env`:
```env
DB_HOST=127.0.0.1
DB_DATABASE=thai_binh_agri
DB_USERNAME=root
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
```

**3. Tạo database và chạy migration**
```bash
mysql -u root -p -e "CREATE DATABASE thai_binh_agri CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
php artisan db:seed
```

**4. Storage link**
```bash
php artisan storage:link
```

**5. Khởi động server**
```bash
php artisan serve
# App chạy tại http://localhost:8000
```

---

## 👤 Tài khoản mặc định

Sau khi chạy seeder:

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | `admin@thaibinh-agri.vn` | `password` |
| Khách hàng | `an.nguyen@example.com` | `password` |
| Khách hàng | `binh.tran@example.com` | `password` |
| Khách hàng | `cuong.le@example.com` | `password` |

---

## 🗺 Routes

### Public

| Method | URL | Tên route | Mô tả |
|---|---|---|---|
| GET | `/` | `home` | Trang chủ |
| GET | `/san-pham` | `products.index` | Danh sách sản phẩm |
| GET | `/san-pham/{slug}` | `products.show` | Chi tiết sản phẩm |
| GET | `/gio-hang` | `cart.index` | Giỏ hàng |
| POST | `/gio-hang/them/{id}` | `cart.add` | Thêm vào giỏ |
| PATCH | `/gio-hang/cap-nhat/{id}` | `cart.update` | Cập nhật số lượng |
| DELETE | `/gio-hang/xoa/{id}` | `cart.remove` | Xóa sản phẩm |
| DELETE | `/gio-hang` | `cart.clear` | Xóa toàn bộ giỏ |
| GET | `/login` | `login` | Form đăng nhập |
| POST | `/login` | — | Xử lý đăng nhập |
| GET | `/register` | `register` | Form đăng ký |
| POST | `/register` | — | Xử lý đăng ký |
| GET | `/thanh-toan/callback` | `payments.callback` | VNPay callback |

### Yêu cầu đăng nhập (`auth`)

| Method | URL | Tên route | Mô tả |
|---|---|---|---|
| POST | `/logout` | `logout` | Đăng xuất |
| GET | `/don-hang` | `orders.index` | Danh sách đơn hàng |
| GET | `/don-hang/{id}` | `orders.show` | Chi tiết đơn hàng |
| POST | `/don-hang` | `orders.store` | Tạo đơn hàng |
| PATCH | `/don-hang/{id}/cancel` | `orders.cancel` | Hủy đơn hàng |
| GET | `/thanh-toan/don-hang/{id}` | `payments.initiate` | Khởi tạo thanh toán |
| GET | `/thanh-toan/ket-qua/{id}` | `payments.result` | Kết quả thanh toán |

### Admin (`auth` + `admin` middleware)

| Method | URL | Tên route | Mô tả |
|---|---|---|---|
| GET | `/admin` | `admin.dashboard` | Dashboard |
| GET/POST | `/admin/products` | `admin.products.*` | CRUD sản phẩm |
| GET | `/admin/orders` | `admin.orders.index` | Danh sách đơn hàng |
| PATCH | `/admin/orders/{id}/status` | `admin.orders.update-status` | Cập nhật trạng thái |
| GET | `/admin/users` | `admin.users.index` | Danh sách người dùng |

---

## ⚙️ Biến môi trường

| Biến | Mô tả | Mặc định |
|---|---|---|
| `APP_NAME` | Tên ứng dụng | Thai Binh Agriculture Commerce |
| `APP_ENV` | Môi trường | `local` |
| `APP_DEBUG` | Hiển thị lỗi chi tiết | `true` |
| `APP_URL` | URL gốc | `http://localhost:8000` |
| `DB_HOST` | MySQL host | `mysql` (Docker) / `127.0.0.1` (local) |
| `DB_DATABASE` | Tên database | `thai_binh_agri` |
| `DB_USERNAME` | MySQL user | `laravel` |
| `DB_PASSWORD` | MySQL password | `secret` |
| `REDIS_HOST` | Redis host | `redis` (Docker) / `127.0.0.1` (local) |
| `CACHE_STORE` | Driver cache | `redis` |
| `SESSION_DRIVER` | Driver session | `redis` |
| `VNPAY_MERCHANT_ID` | Mã merchant VNPay | — |
| `VNPAY_SECRET_KEY` | Secret key VNPay | — |
| `VNPAY_URL` | URL cổng thanh toán | Sandbox URL |
| `VNPAY_RETURN_URL` | URL callback sau thanh toán | `http://localhost:8000/payment/callback` |

---

## 📝 Ghi chú phát triển

### Thêm module mới

1. Tạo thư mục `app/Modules/TenModule/` với cấu trúc: `Controllers/`, `Services/`, `Routes/`, `Views/`, `Providers/`
2. Tạo `TenModuleServiceProvider` extends `ServiceProvider`, load routes và views
3. Đăng ký provider trong `bootstrap/app.php`

### Thêm repository mới

1. Tạo interface trong `app/Repositories/Contracts/`
2. Tạo implementation trong `app/Repositories/Eloquent/`
3. Thêm binding vào `$bindings` array trong `RepositoryServiceProvider`

### Coding conventions

- **Enums** thay vì magic strings cho status fields
- **Return type hints** bắt buộc trên tất cả methods
- **DB transactions** cho mọi operation thay đổi nhiều bảng (tạo order, hủy order)
- **Pessimistic locking** (`lockForUpdate`) khi đọc stock để tránh race condition
- **Slug** tự động generate qua `HasSlug` trait
