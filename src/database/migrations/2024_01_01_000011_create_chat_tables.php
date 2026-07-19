<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Conversations table
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->enum('mode', ['bot', 'live'])->default('bot');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('taken_over_at')->nullable();
            $table->string('taken_over_by_name')->nullable();
            $table->timestamp('handed_back_at')->nullable();
            $table->string('handed_back_by_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('mode');
            $table->index('is_active');
        });

        // Messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('sender_type', ['customer', 'bot', 'admin', 'system']);
            $table->text('body');
            $table->boolean('is_read_by_admin')->default(false);
            $table->boolean('is_read_by_customer')->default(false);
            $table->timestamps();

            $table->index('conversation_id');
            $table->index('sender_type');
            $table->index('is_read_by_admin');
            $table->index('created_at');
        });

        // Knowledge base table
        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->string('question', 500);
            $table->text('answer');
            $table->string('category', 100)->default('umum');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('category');
        });

        // Chat settings table
        Schema::create('chat_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('knowledge_bases');
        Schema::dropIfExists('chat_settings');
    }
};
