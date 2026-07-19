<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@multibase.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '08123456789',
        ]);

        // Create Production User
        User::create([
            'name' => 'Production Staff',
            'email' => 'production@multibase.com',
            'password' => Hash::make('password'),
            'role' => 'production',
            'phone' => '08123456790',
        ]);

        // Create Customer User
        User::create([
            'name' => 'Customer Test',
            'email' => 'customer@test.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '08123456791',
        ]);

        // Create Sample Products
        $products = [
            [
                'name' => 'Pagar Minimalis',
                'description' => 'Pagar minimalis modern dengan desain elegan, terbuat dari besi berkualitas tinggi dengan finishing cat powder coating.',
                'price' => 1500000,
                'material' => 'Besi',
                'category' => 'Pagar',
                'specifications' => [
                    'tinggi' => '1.5 meter',
                    'lebar_panel' => '2 meter',
                    'ketebalan' => '2mm',
                    'finishing' => 'Powder Coating',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Kanopi Baja Ringan',
                'description' => 'Kanopi dengan rangka baja ringan, atap polycarbonate. Cocok untuk carport atau teras rumah.',
                'price' => 2500000,
                'material' => 'Baja Ringan',
                'category' => 'Kanopi',
                'specifications' => [
                    'ukuran' => '4m x 6m',
                    'atap' => 'Polycarbonate 5mm',
                    'rangka' => 'Baja Ringan 0.75mm',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Teralis Jendela',
                'description' => 'Teralis jendela dengan motif klasik, memberikan keamanan sekaligus estetika untuk rumah Anda.',
                'price' => 850000,
                'material' => 'Besi',
                'category' => 'Teralis',
                'specifications' => [
                    'ukuran_standar' => '120cm x 100cm',
                    'diameter_besi' => '12mm',
                    'finishing' => 'Cat Meni + Cat Prada',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Railing Tangga Stainless',
                'description' => 'Railing tangga stainless steel 304, modern dan tahan karat. Cocok untuk interior rumah minimalis.',
                'price' => 3200000,
                'material' => 'Stainless Steel',
                'category' => 'Railing',
                'specifications' => [
                    'material' => 'SS 304',
                    'pipa' => '1.5 inch',
                    'tinggi' => '90cm',
                    'sistem' => 'Welding',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Pintu Garasi Sliding',
                'description' => 'Pintu garasi sliding otomatis dengan sistem remote control. Kuat dan praktis.',
                'price' => 8500000,
                'material' => 'Galvanis',
                'category' => 'Pintu',
                'specifications' => [
                    'ukuran' => '3m x 2.5m',
                    'sistem' => 'Automatic Sliding',
                    'motor' => 'DC 600W',
                    'material' => 'Galvanis + Panel',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Mezzanine Besi',
                'description' => 'Konstruksi mezzanine besi untuk menambah ruang lantai di gudang atau toko.',
                'price' => 12000000,
                'material' => 'Besi',
                'category' => 'Konstruksi',
                'specifications' => [
                    'ukuran' => '4m x 6m',
                    'beban_maksimal' => '500kg/m2',
                    'tinggi' => '3 meter',
                    'lantai' => 'Plat Besi 3mm',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->call(OwnerSeeder::class);
    }
}
