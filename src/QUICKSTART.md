# QUICK START GUIDE - Multibase Engineering

## Setup Cepat (Jika database sudah di-migrate)

Jika Anda baru clone/download project ini dan ingin menjalankannya dengan cepat:

```bash
# 1. Install dependencies (jika belum)
composer install

# 2. Copy environment file (jika belum ada .env)
cp .env.example .env

# 3. Generate key (jika belum)
php artisan key:generate

# 4. Konfigurasi database di .env
# Edit file .env dan sesuaikan:
DB_CONNECTION=mysql
DB_DATABASE=multibase_engineering
DB_USERNAME=root
DB_PASSWORD=

# 5. Refresh database dengan data awal
php artisan migrate:fresh --seed

# 6. Create storage link
php artisan storage:link

# 7. Jalankan server
php artisan serve
```

Aplikasi akan jalan di: http://localhost:8000

## Test Accounts

Setelah seeding, gunakan akun-akun ini untuk testing:

### Admin
- **Email**: admin@multibase.com
- **Password**: password
- **Akses**: Dashboard admin, kelola pesanan, verifikasi pembayaran, kelola produk

### Production Staff
- **Email**: production@multibase.com
- **Password**: password
- **Akses**: Dashboard produksi, update progress produksi

### Customer
- **Email**: customer@test.com
- **Password**: password
- **Akses**: Dashboard customer, pesan produk katalog/custom, upload pembayaran, tracking

## Langkah-langkah Testing

### A. Testing sebagai Customer

1. **Login** dengan credentials customer
2. **Lihat Katalog**
   - Klik menu "Katalog Produk"
   - Browse produk yang tersedia
   - Klik "Detail" pada produk yang diminati
3. **Pesan Produk Katalog**
   - Di halaman detail produk, klik "Pesan Produk Ini"
   - Isi jumlah yang diinginkan
   - Tambahkan catatan (optional)
   - Klik "Buat Pesanan"
4. **Buat Pesanan Custom**
   - Klik menu "Custom Produk"
   - Isi form lengkap (nama produk, material, ukuran, dll)
   - Upload file desain (optional)
   - Klik "Kirim Pesanan"
5. **Lihat Pesanan**
   - Klik "Pesanan Saya" atau "Dashboard"
   - Lihat status dan detail pesanan

### B. Testing sebagai Admin

1. **Login** dengan credentials admin
2. **Dashboard Overview**
   - Lihat statistik pesanan
   - Lihat pending payments dan recent orders
3. **Verifikasi Pesanan**
   - Klik menu "Pesanan"
   - Pilih pesanan dengan status "pending"
   - Klik "Detail"
   - Review spesifikasi pesanan
   - Update status ke "verified"
4. **Set Harga (untuk custom order)**
   - Pada detail pesanan custom
   - Input total harga dan estimasi selesai
   - Klik "Update"
5. **Verifikasi Pembayaran**
   - Klik menu "Pembayaran"
   - Review bukti pembayaran yang diupload customer
   - Klik "Verifikasi" untuk approve atau "Tolak" untuk reject
6. **Kirim ke Produksi**
   - Pada pesanan yang sudah paid
   - Klik "Kirim ke Produksi"
7. **Kelola Produk**
   - Klik menu "Produk"
   - Tambah, edit, atau hapus produk katalog

### C. Testing sebagai Production

1. **Login** dengan credentials production
2. **Dashboard Produksi**
   - Lihat pesanan yang masuk ke produksi
   - Lihat statistik by stage
3. **Update Progress**
   - Klik "Update Progress" pada pesanan
   - Pilih stage: pending → in_progress → finishing → completed
   - Input catatan progress
   - Klik "Update"

## Alur Lengkap End-to-End

### Skenario: Pesanan Custom dari Customer hingga Selesai

1. **Customer** membuat pesanan custom
2. **Admin** menerima notifikasi pesanan baru di dashboard
3. **Admin** review pesanan, set harga Rp 5.000.000 dan estimasi 14 hari
4. **Admin** update status ke "verified"
5. **Customer** melihat harga di dashboard, melakukan pembayaran, upload bukti
6. **Admin** verifikasi pembayaran, approve
7. **Admin** klik "Kirim ke Produksi"
8. **Production** lihat pesanan baru di dashboard
9. **Production** mulai kerjakan, update stage ke "in_progress"
10. **Customer** melihat status berubah di tracking
11. **Production** update stage ke "finishing"
12. **Production** selesai, update stage ke "completed"
13. **Customer** melihat status "completed", bisa koordinasi pengambilan

## Troubleshooting Umum

### Error: Class 'App\Models\OrderItem' not found
```bash
composer dump-autoload
```

### Error: Storage link not working
```bash
php artisan storage:link
```

### Error: SQLSTATE connection refused
- Pastikan MySQL/PostgreSQL sudah running
- Cek kredensial di file `.env`

### Error: 419 Page Expired
- Clear cache: `php artisan cache:clear`
- Clear config: `php artisan config:clear`

### Error: 500 Internal Server Error
- Cek log: `storage/logs/laravel.log`
- Pastikan permission folder storage dan bootstrap/cache: `chmod -R 775 storage bootstrap/cache`

## Struktur Folder Penting

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── Customer/
│   │   ├── Admin/
│   │   └── Production/
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Policies/
│       └── OrderPolicy.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── OrderFile.php
│   ├── Payment.php
│   └── ProductionLog.php

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── auth/
│   ├── customer/
│   ├── admin/
│   └── production/

routes/
└── web.php

database/
├── migrations/
└── seeders/
    └── DatabaseSeeder.php
```

## Catatan Penting

1. **File Upload**: 
   - Pastikan folder `storage/app/public` writable
   - Storage link sudah dibuat dengan `php artisan storage:link`

2. **Roles**:
   - Customer: dapat membuat pesanan, upload pembayaran, tracking
   - Admin: full control, verifikasi semua, kelola master data
   - Production: hanya update progress produksi

3. **Status Flow**:
   - pending → verified → paid → in_production → completed
   - rejected: pesanan ditolak admin

4. **Production Stages**:
   - pending → in_progress → finishing → completed

## Support

Jika ada error atau pertanyaan:
1. Cek `storage/logs/laravel.log`
2. Pastikan semua dependencies terinstall
3. Pastikan PHP version 8.2+
4. Pastikan database connection OK

NOTES:
jika customer sudah membayar DP, maka data masuk ke production dan bisa segera dikerjakan.
