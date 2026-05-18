<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Gạo & Lúa',
                'description' => 'Các loại gạo và lúa chất lượng cao từ đồng bằng sông Hồng',
                'slug'        => 'gao-lua',
            ],
            [
                'name'        => 'Rau Củ Quả',
                'description' => 'Rau củ quả tươi sạch, trồng theo tiêu chuẩn VietGAP',
                'slug'        => 'rau-cu-qua',
            ],
            [
                'name'        => 'Thủy Sản',
                'description' => 'Tôm, cá, cua và các loại thủy sản tươi sống',
                'slug'        => 'thuy-san',
            ],
            [
                'name'        => 'Gia Cầm & Thịt',
                'description' => 'Thịt gà, vịt, lợn và các sản phẩm chăn nuôi sạch',
                'slug'        => 'gia-cam-thit',
            ],
            [
                'name'        => 'Trái Cây',
                'description' => 'Trái cây nhiệt đới tươi ngon, thu hoạch theo mùa',
                'slug'        => 'trai-cay',
            ],
            [
                'name'        => 'Nông Sản Khô',
                'description' => 'Đậu, ngô, khoai, sắn và các nông sản khô chế biến',
                'slug'        => 'nong-san-kho',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
