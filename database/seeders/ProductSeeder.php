<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryMap = Category::pluck('id', 'slug');

        $products = [
            // Gạo & Lúa
            [
                'name'        => 'Gạo ST25',
                'description' => 'Gạo ST25 - giống gạo ngon nhất thế giới, hạt dài, cơm dẻo thơm đặc trưng.',
                'price'       => 35000,
                'category'    => 'gao-lua',
                'stock'       => 500,
                'status'      => 'active',
            ],
            [
                'name'        => 'Gạo Tám Xoan Thái Bình',
                'description' => 'Gạo Tám Xoan đặc sản Thái Bình, hương thơm tự nhiên, cơm mềm dẻo.',
                'price'       => 28000,
                'category'    => 'gao-lua',
                'stock'       => 300,
                'status'      => 'active',
            ],
            [
                'name'        => 'Gạo Nếp Cái Hoa Vàng',
                'description' => 'Nếp cái hoa vàng truyền thống, dùng làm xôi, bánh chưng, rượu nếp.',
                'price'       => 32000,
                'category'    => 'gao-lua',
                'stock'       => 200,
                'status'      => 'active',
            ],

            // Rau Củ Quả
            [
                'name'        => 'Rau Muống Sạch',
                'description' => 'Rau muống trồng theo tiêu chuẩn VietGAP, không thuốc trừ sâu.',
                'price'       => 8000,
                'category'    => 'rau-cu-qua',
                'stock'       => 150,
                'status'      => 'active',
            ],
            [
                'name'        => 'Cà Chua Bi',
                'description' => 'Cà chua bi đỏ tươi, ngọt, giàu vitamin C và lycopene.',
                'price'       => 15000,
                'category'    => 'rau-cu-qua',
                'stock'       => 200,
                'status'      => 'active',
            ],
            [
                'name'        => 'Khoai Lang Mật',
                'description' => 'Khoai lang mật Thái Bình, ruột vàng, vị ngọt tự nhiên.',
                'price'       => 12000,
                'category'    => 'rau-cu-qua',
                'stock'       => 400,
                'status'      => 'active',
            ],

            // Thủy Sản
            [
                'name'        => 'Tôm Sú Tươi',
                'description' => 'Tôm sú nuôi tại vùng ven biển Thái Bình, tươi sống, kích cỡ đồng đều.',
                'price'       => 180000,
                'category'    => 'thuy-san',
                'stock'       => 80,
                'status'      => 'active',
            ],
            [
                'name'        => 'Cá Trắm Cỏ',
                'description' => 'Cá trắm cỏ nuôi ao sạch, thịt chắc, ít xương dăm.',
                'price'       => 65000,
                'category'    => 'thuy-san',
                'stock'       => 100,
                'status'      => 'active',
            ],
            [
                'name'        => 'Ngao Sò Tươi',
                'description' => 'Ngao sò khai thác tự nhiên vùng biển Thái Bình, tươi ngon.',
                'price'       => 45000,
                'category'    => 'thuy-san',
                'stock'       => 120,
                'status'      => 'active',
            ],

            // Gia Cầm & Thịt
            [
                'name'        => 'Gà Ta Thả Vườn',
                'description' => 'Gà ta nuôi thả vườn, thịt chắc, da vàng, không dùng chất tăng trọng.',
                'price'       => 120000,
                'category'    => 'gia-cam-thit',
                'stock'       => 50,
                'status'      => 'active',
            ],
            [
                'name'        => 'Vịt Bầu',
                'description' => 'Vịt bầu nuôi đồng, thịt thơm ngon, phù hợp nấu cháo, vịt quay.',
                'price'       => 95000,
                'category'    => 'gia-cam-thit',
                'stock'       => 60,
                'status'      => 'active',
            ],

            // Trái Cây
            [
                'name'        => 'Ổi Lê Đài Loan',
                'description' => 'Ổi lê giòn ngọt, ít hạt, trồng theo quy trình sạch.',
                'price'       => 25000,
                'category'    => 'trai-cay',
                'stock'       => 180,
                'status'      => 'active',
            ],
            [
                'name'        => 'Chuối Tiêu Hồng',
                'description' => 'Chuối tiêu hồng đặc sản, vị ngọt đậm, thơm tự nhiên.',
                'price'       => 18000,
                'category'    => 'trai-cay',
                'stock'       => 250,
                'status'      => 'active',
            ],

            // Nông Sản Khô
            [
                'name'        => 'Đậu Xanh Hạt Tiêu',
                'description' => 'Đậu xanh hạt tiêu loại 1, hạt đều, không mốc, dùng nấu chè, làm nhân bánh.',
                'price'       => 42000,
                'category'    => 'nong-san-kho',
                'stock'       => 300,
                'status'      => 'active',
            ],
            [
                'name'        => 'Ngô Nếp Khô',
                'description' => 'Ngô nếp phơi khô tự nhiên, dùng rang, xay bột hoặc nấu cháo.',
                'price'       => 20000,
                'category'    => 'nong-san-kho',
                'stock'       => 350,
                'status'      => 'active',
            ],
        ];

        foreach ($products as $product) {
            $categorySlug = $product['category'];
            unset($product['category']);

            // Slug sẽ được tự động tạo bởi HasSlug trait
            Product::create(array_merge($product, [
                'category_id' => $categoryMap[$categorySlug],
                'sold_count'  => rand(0, 200),
                'view_count'  => rand(0, 1000),
            ]));
        }
    }
}
