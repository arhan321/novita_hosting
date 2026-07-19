<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->enum('type', ['katalog', 'custom']); // jenis pesanan
            $table->enum('status', [
                'pending',      // menunggu verifikasi
                'verified',     // sudah diverifikasi
                'paid',         // pembayaran dikonfirmasi
                'in_production', // sedang diproduksi
                'completed',    // selesai
                'rejected'      // ditolak
            ])->default('pending');
            $table->text('notes')->nullable(); // catatan dari pelanggan
            $table->decimal('total_price', 12, 2)->nullable();
            $table->date('estimated_completion')->nullable();
            $table->timestamps();

            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->index('user_id');
            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
