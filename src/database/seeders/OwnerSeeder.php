<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Owner user
        User::create([
            'name' => 'Owner Multi Base',
            'email' => 'owner@multibase.com',
            'password' => Hash::make('password123'),
            'phone' => '081234567890',
            'role' => 'owner',
            'is_active' => true,
        ]);

        $this->command->info('Owner user created successfully!');
        $this->command->info('Email: owner@multibase.com');
        $this->command->info('Password: password123');
    }
}
