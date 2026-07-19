<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('shipping_method', ['pickup', 'internal', 'per_km'])->default('pickup')->after('total_price');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('shipping_method');
            $table->text('customer_address')->nullable()->after('shipping_cost');
            $table->decimal('distance_km', 8, 2)->nullable()->after('customer_address');
            $table->decimal('customer_latitude', 10, 8)->nullable()->after('distance_km');
            $table->decimal('customer_longitude', 11, 8)->nullable()->after('customer_latitude');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_method',
                'shipping_cost',
                'customer_address',
                'distance_km',
                'customer_latitude',
                'customer_longitude'
            ]);
        });
    }
};
