<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_address', 500)->nullable();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('province_id');
            $table->dropConstrainedForeignId('ward_id');
            $table->dropColumn('shipping_address');
        });
    }
};
