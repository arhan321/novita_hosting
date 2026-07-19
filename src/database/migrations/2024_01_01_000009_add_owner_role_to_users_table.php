<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // MySQL supports MODIFY COLUMN for ENUM
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'admin', 'production', 'owner') NOT NULL DEFAULT 'customer'");
        }
        // SQLite stores ENUM as string, so 'owner' is already a valid value — no action needed
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'admin', 'production') NOT NULL DEFAULT 'customer'");
        }
    }
};
