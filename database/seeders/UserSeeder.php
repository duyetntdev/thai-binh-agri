<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::create([
            'name'              => 'Admin',
            'email'             => 'admin@thaibinh-agri.vn',
            'password'          => Hash::make('password'),
            'phone'             => '0912345678',
            'address'           => '123 Đường Lý Bôn',
            'city'              => 'Thái Bình',
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        // Sample customers
        $customers = [
            [
                'name'    => 'Nguyễn Văn An',
                'email'   => 'an.nguyen@example.com',
                'phone'   => '0901111111',
                'address' => '45 Phố Trần Hưng Đạo',
                'city'    => 'Thái Bình',
            ],
            [
                'name'    => 'Trần Thị Bình',
                'email'   => 'binh.tran@example.com',
                'phone'   => '0902222222',
                'address' => '78 Đường Quang Trung',
                'city'    => 'Thái Bình',
            ],
            [
                'name'    => 'Lê Văn Cường',
                'email'   => 'cuong.le@example.com',
                'phone'   => '0903333333',
                'address' => '12 Phố Hoàng Diệu',
                'city'    => 'Thái Bình',
            ],
            [
                'name'    => 'Phạm Thị Dung',
                'email'   => 'dung.pham@example.com',
                'phone'   => '0904444444',
                'address' => '56 Đường Lê Lợi',
                'city'    => 'Thái Bình',
            ],
            [
                'name'    => 'Hoàng Văn Em',
                'email'   => 'em.hoang@example.com',
                'phone'   => '0905555555',
                'address' => '90 Phố Phan Bội Châu',
                'city'    => 'Thái Bình',
            ],
        ];

        foreach ($customers as $customer) {
            User::create(array_merge($customer, [
                'password'          => Hash::make('password'),
                'role'              => 'customer',
                'email_verified_at' => now(),
            ]));
        }
    }
}
