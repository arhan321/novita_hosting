<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->enum('stage', ['pending', 'in_progress', 'finishing', 'completed']);
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade'); // admin/production
            $table->timestamps();

            $table->index('order_id');
            $table->index('stage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_logs');
    }
};
