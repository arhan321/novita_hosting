<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->enum('file_type', ['design', 'payment_proof']); // jenis file
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamps();

            $table->index('order_id');
            $table->index('file_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_files');
    }
};
