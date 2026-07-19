# Multibase Engineering - Aplikasi Pemesanan Fabrikasi dan Logam

## Deskripsi
Aplikasi MVP untuk pemesanan dan manajemen produksi fabrikasi dan logam dengan 3 role user: Customer, Admin, dan Production.

## Fitur Utama

### Customer
- Registrasi dan Login
- Lihat katalog produk
- Pesan produk dari katalog
- Buat pesanan custom dengan upload desain
- Upload bukti pembayaran  
- Tracking status pesanan
- Lihat riwayat pesanan

### Admin
- Dashboard monitoring
- Verifikasi pesanan baru
- Verifikasi pembayaran
- Set harga final untuk pesanan custom
- Kirim pesanan ke produksi
- Manajemen produk katalog
- Update status pesanan
- Laporan pesanan

### Production
- Dashboard produksi
- Lihat pesanan yang masuk ke produksi
- Update progress produksi (pending, in progress, finishing, completed)
- Input catatan progress

## Teknologi
- **Framework**: Laravel 12
- **Database**: MySQL/PostgreSQL
- **Frontend**: Blade + Tailwind CSS
- **Authentication**: Laravel Auth

## Instalasi

### Persyaratan
- PHP 8.2 atau lebih tinggi
- Composer
- MySQL/PostgreSQL
- Node.js & NPM

### Langkah Instalasi

1. **Clone atau download project**
```bash
cd /Users/yogahermawan/Development/PROJECT/multibase-engineering
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Konfigurasi database di `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multibase_engineering
DB_USERNAME=root
DB_PASSWORD=
```

5. **Jalankan migration dan seeder**
```bash
php artisan migrate:fresh --seed
```

6. **Create storage link**
```bash
php artisan storage:link
```

7. **Build assets**
```bash
npm run build
```

8. **Jalankan server**
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## Default Users (Setelah Seeding)

### Admin
- Email: `admin@multibase.com`
- Password: `password`

### Production
- Email: `production@multibase.com`
- Password: `password`

### Customer
- Email: `customer@test.com`
- Password: `password`

## Alur Kerja Aplikasi

### 1. Alur Customer
1. Register/Login
2. Pilih menu: Katalog Produk atau Custom Produk
3. **Jika Katalog**: Pilih produk → Isi jumlah → Buat pesanan
4. **Jika Custom**: Isi form spesifikasi → Upload desain → Kirim pesanan
5. Tunggu verifikasi admin
6. Upload bukti pembayaran setelah harga ditentukan
7. Tracking status produksi
8. Terima produk jika status selesai

### 2. Alur Admin
1. Login ke dashboard admin
2. Lihat pesanan masuk (pending)
3. Verifikasi spesifikasi pesanan
4. Untuk custom: Set harga final dan estimasi
5. Verifikasi pembayaran customer
6. Kirim pesanan ke produksi
7. Monitor progress dari production
8. Update status final ke completed

### 3. Alur Production
1. Login ke dashboard production
2. Lihat daftar pesanan yang masuk
3. Mulai pengerjaan
4. Update progress: pending → in progress → finishing → completed
5. Input catatan setiap progress
6. Tandai selesai jika produksi complete

## Struktur Database

### Users
- id, name, email, password, phone, role (customer/admin/production), is_active

### Products
- id, name, description, price, material, category, specifications (JSON), image_path, is_active

### Orders
- id, user_id, order_number, type (katalog/custom), status, notes, total_price, estimated_completion, verified_by, verified_at

### Order Items
- id, order_id, product_id (nullable untuk custom), product_name, quantity, unit_price, subtotal, specifications (JSON)

### Order Files
- id, order_id, file_type (design/payment_proof), file_path, file_name

### Payments
- id, order_id, payment_method, amount, payment_proof, status, verified_by, verified_at, notes

### Production Logs
- id, order_id, stage (pending/in_progress/finishing/completed), notes, updated_by

## Status Pesanan

- **pending**: Menunggu verifikasi admin
- **verified**: Sudah diverifikasi, menunggu pembayaran
- **paid**: Pembayaran sudah diverifikasi
- **in_production**: Sedang dalam proses produksi
- **completed**: Selesai
- **rejected**: Ditolak

## Routes Penting

### Public
- `/` - Homepage
- `/login` - Login
- `/register` - Register

### Customer
- `/customer/dashboard` - Dashboard customer
- `/customer/products` - Katalog produk
- `/customer/orders` - Daftar pesanan
- `/customer/orders/custom` - Form custom order

### Admin
- `/admin/dashboard` - Dashboard admin
- `/admin/orders` - Manajemen pesanan
- `/admin/payments` - Verifikasi pembayaran
- `/admin/products` - Manajemen produk

### Production
- `/production/dashboard` - Dashboard produksi
- `/production/orders` - Pesanan produksi

## File Penting

### Controllers
- `AuthController.php` - Authentication
- `Customer/DashboardController.php` - Customer dashboard
- `Customer/ProductController.php` - Katalog produk
- `Customer/OrderController.php` - Pemesanan customer
- `Customer/PaymentController.php` - Pembayaran
- `Admin/DashboardController.php` - Admin dashboard
- `Admin/OrderController.php` - Manajemen pesanan
- `Admin/PaymentController.php` - Verifikasi pembayaran
- `Admin/ProductController.php` - Manajemen produk
- `Production/DashboardController.php` - Production dashboard
- `Production/OrderController.php` - Update produksi

### Middleware
- `RoleMiddleware.php` - Role-based access control

### Policies
- `OrderPolicy.php` - Authorization untuk order

## Development

### Menjalankan di mode development
```bash
# Terminal 1 - Laravel server
php artisan serve

# Terminal 2 - Vite dev server (jika pakai hot reload)
npm run dev
```

### Testing
```bash
php artisan test
```

## Catatan Pengembangan Selanjutnya

### Fitur yang bisa ditambahkan:
1. **Notification System** - Email/SMS notification untuk setiap perubahan status
2. **File Preview** - Preview file desain yang diupload
3. **Chat System** - Chat antara customer dan admin
4. **Reports** - Laporan penjualan dan produksi yang lebih detail
5. **Invoice Generator** - Generate invoice otomatis
6. **Payment Gateway** - Integrasi payment gateway (Midtrans, dll)
7. **Gallery** - Portfolio produk yang sudah dikerjakan
8. **Quotation System** - Sistem quotation sebelum order
9. **Material Calculator** - Kalkulator estimasi material
10. **Mobile App** - React Native atau Flutter

## Troubleshooting

### Error: storage link not found
```bash
php artisan storage:link
```

### Error: Class not found
```bash
composer dump-autoload
```

### Error: Migration failed
```bash
php artisan migrate:fresh --seed
```

### Error: Permission denied (storage)
```bash
chmod -R 775 storage bootstrap/cache
```

## Support
Untuk bantuan atau pertanyaan, silakan buat issue di repository.

## License
MIT License
