<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->foreignId('province_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('division_type');
            $table->string('codename');
            $table->timestamps();

            $table->index(['province_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
