<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('specifications');
            $table->integer('min_order')->default(1)->after('stock');
            $table->string('unit', 50)->nullable()->after('min_order');
            $table->boolean('is_available')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'min_order', 'unit', 'is_available']);
        });
    }
};
