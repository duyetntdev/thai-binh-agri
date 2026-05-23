<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::getConnection()->table('menu_items')->insert([
            ['label' => 'Giới thiệu', 'url' => '/gioi-thieu', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Liên hệ', 'url' => '/lien-he', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Tin tức', 'url' => '/tin-tuc', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Chính sách', 'url' => '/chinh-sach', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
