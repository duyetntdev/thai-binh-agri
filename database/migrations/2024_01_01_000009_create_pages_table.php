<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::getConnection()->table('pages')->insert([
            [
                'title' => 'Giới thiệu',
                'slug' => 'gioi-thieu',
                'meta_title' => 'Giới thiệu — Nông Sản Thái Bình',
                'content' => '<p>Nông Sản Thái Bình chuyên cung cấp các loại nông sản sạch, an toàn và chất lượng cao từ vùng đồng bằng Thái Bình.</p><p>Chúng tôi cam kết mang tới trải nghiệm mua sắm trực tuyến tiện lợi, minh bạch và đáng tin cậy cho người tiêu dùng.</p><p>Với phương châm “Từ nông dân đến tay người tiêu dùng”, Nông Sản Thái Bình luôn đồng hành cùng sự phát triển bền vững của cộng đồng nông nghiệp địa phương.</p>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Liên hệ',
                'slug' => 'lien-he',
                'meta_title' => 'Liên hệ — Nông Sản Thái Bình',
                'content' => '<p>Mọi thắc mắc và yêu cầu hỗ trợ xin vui lòng liên hệ với chúng tôi qua các kênh sau.</p><div><strong>Địa chỉ:</strong> 123 Đường Lý Bôn, TP. Thái Bình</div><div><strong>Điện thoại:</strong> 0912 345 678</div><div><strong>Email:</strong> info@thaibinh-agri.vn</div><div><strong>Giờ làm việc:</strong> Thứ 2 - Thứ 7: 8:00 - 18:00</div>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Tin tức',
                'slug' => 'tin-tuc',
                'meta_title' => 'Tin tức — Nông Sản Thái Bình',
                'content' => '<p>Theo dõi các thông tin mới nhất về giá cả nông sản, chương trình khuyến mãi và các sản phẩm đặc sắc từ Nông Sản Thái Bình.</p><ul><li>Chương trình ưu đãi mùa vụ mới.</li><li>Sản phẩm đạt chuẩn an toàn thực phẩm.</li><li>Cập nhật thông tin nông nghiệp địa phương.</li></ul>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Chính sách',
                'slug' => 'chinh-sach',
                'meta_title' => 'Chính sách — Nông Sản Thái Bình',
                'content' => '<p>Chính sách bảo mật, điều kiện giao hàng và quyền lợi khách hàng của Nông Sản Thái Bình được thiết kế nhằm đảm bảo trải nghiệm mua sắm an toàn và minh bạch.</p>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
