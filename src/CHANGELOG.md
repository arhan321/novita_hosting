# Changelog - Multi Base Engineering

## [2.0.0] - 2026-04-25

### 🎨 Desain UI/UX Baru

#### Warna Baru
- **Primary**: Navy Blue (#102a43) - Profesional & Engineering
- **Secondary**: Abu-abu + Putih - Clean & Modern  
- **Accent**: Orange (#f97316) - Call-to-Action buttons

#### Perubahan Visual
- Navigasi dengan background navy-800
- Hover effects dengan warna orange
- Card design dengan shadow dan border
- Footer dengan background navy-800
- Icon Font Awesome untuk visual yang lebih baik

### 👤 Role Owner Baru

#### Fitur Owner
- **Dashboard Owner** (`/owner/dashboard`)
  - Total pendapatan (verified payments)
  - Total pesanan
  - Pesanan selesai
  - Pembayaran pending
  - Grafik pendapatan 6 bulan terakhir
  - Daftar pesanan terbaru

- **Laporan Keuangan** (`/owner/reports/financial`)
  - Filter berdasarkan tanggal
  - Total pemasukan periode
  - Rekap pesanan
  - Pemasukan harian
  - Detail pembayaran
  - Export ke Excel/CSV

- **Invoice Management** (`/owner/invoices`)
  - Lihat semua invoice
  - Filter dan search
  - Download PDF
  - Print invoice

#### Files Baru
- `app/Http/Controllers/Owner/DashboardController.php`
- `app/Http/Controllers/Owner/ReportController.php`
- `app/Http/Controllers/Owner/InvoiceController.php`
- `resources/views/owner/dashboard.blade.php`
- `resources/views/owner/reports/financial.blade.php`
- `resources/views/owner/invoices/index.blade.php`
- `resources/views/owner/invoices/show.blade.php`
- `resources/views/owner/invoices/pdf.blade.php`
- `resources/views/owner/invoices/print.blade.php`

### 🚚 Fitur Pengiriman

#### Opsi Pengiriman
1. **Ambil Sendiri** - Rp 0
2. **Jasa Pribadi (Internal)** - GRATIS dengan syarat:
   - Minimal order: Rp 500.000
   - Maksimal jarak: 30 km dari lokasi perusahaan
3. **Per Kilometer** - Rp 5.000/km
   - Otomatis kalkulasi jarak
   - Menggunakan Haversine formula

#### Lokasi Perusahaan
- **Alamat**: Multi Base Engineering, Ruko Fiorenza, Jl. Raya H. Mirza Cinde Lakoni Jl. Citra Raya Boulevard, Ciakar, Kec. Panongan, Kabupaten Tangerang, Banten 15710
- **Koordinat**: -6.1754, 106.5772

#### Fitur Geolocation
- Auto-detect lokasi customer
- Search alamat menggunakan Nominatim API
- Kalkulasi jarak otomatis
- Validasi syarat pengiriman

#### Database Changes
- Migration: `2024_01_01_000008_add_shipping_to_orders_table.php`
- Kolom baru di tabel `orders`:
  - `shipping_method` (enum: pickup, internal, per_km)
  - `shipping_cost` (decimal)
  - `customer_address` (text)
  - `distance_km` (decimal)
  - `customer_latitude` (decimal)
  - `customer_longitude` (decimal)

#### Files Baru
- `resources/js/shipping-calculator.js`

### 📄 Dashboard Invoice (Admin)

#### Fitur Invoice
- **List Invoice** (`/admin/invoices`)
  - Filter berdasarkan status, tanggal, customer
  - Search by order number atau nama customer
  - Pagination
  - Quick actions: View, PDF, Print

- **Detail Invoice** (`/admin/invoices/{order}`)
  - Informasi lengkap pesanan
  - Detail produk dengan qty dan harga
  - Informasi pengiriman
  - Status pembayaran dan produksi
  - Riwayat pembayaran

- **Download PDF** (`/admin/invoices/{order}/pdf`)
  - Format profesional
  - Logo dan informasi perusahaan
  - Detail lengkap pesanan

- **Print Invoice** (`/admin/invoices/{order}/print`)
  - Optimized untuk print
  - Button print otomatis
  - Format clean tanpa navigasi

#### Files Baru
- `app/Http/Controllers/Admin/InvoiceController.php`
- `resources/views/admin/invoices/index.blade.php`
- `resources/views/admin/invoices/show.blade.php`
- `resources/views/admin/invoices/pdf.blade.php`
- `resources/views/admin/invoices/print.blade.php`

### 🔧 Technical Changes

#### Dependencies Baru
- `barryvdh/laravel-dompdf` - PDF generation

#### Model Updates
- `app/Models/Order.php`
  - Added shipping fields to fillable
  - Added shipping cost calculation methods
  - Added `getTotalWithShippingAttribute()`
  - Added `calculateShippingCost()`

- `app/Models/User.php`
  - Added `isOwner()` method
  - Added `isProduction()` method

#### Controller Updates
- `app/Http/Controllers/Customer/OrderController.php`
  - Added shipping method validation
  - Added distance calculation
  - Added shipping cost calculation
  - Added `calculateDistance()` method

#### Routes Updates
- Added Owner routes group
- Added Invoice routes for Admin and Owner
- Added Financial Report routes for Owner

#### Middleware
- `RoleMiddleware` sudah support multiple roles (tidak perlu diubah)

### 📝 Files Modified

#### Core Files
- `resources/css/app.css` - Added custom color palette
- `resources/views/layouts/app.blade.php` - Updated with new design
- `routes/web.php` - Added new routes
- `composer.json` - Added dompdf package

#### Migration Files
- `database/migrations/2024_01_01_000008_add_shipping_to_orders_table.php` (NEW)

### 📚 Documentation

#### New Documentation Files
- `INSTALLATION_GUIDE.md` - Panduan instalasi lengkap
- `CHANGELOG.md` - Dokumentasi perubahan (file ini)

### 🎯 Breaking Changes

#### Database
- Perlu menjalankan migration baru untuk kolom shipping
- Existing orders akan memiliki `shipping_method` = 'pickup' (default)

#### User Roles
- Perlu membuat user dengan role 'owner' secara manual

### 🔜 Future Improvements

- [ ] Integration dengan Google Maps API untuk lebih akurat
- [ ] Real-time tracking pengiriman
- [ ] Notifikasi email untuk invoice
- [ ] Dashboard analytics yang lebih detail
- [ ] Multi-currency support
- [ ] Automated backup untuk invoice

### 📞 Support

Untuk pertanyaan atau issue terkait update ini, silakan hubungi tim development.

---

**Version**: 2.0.0  
**Release Date**: April 25, 2026  
**Author**: Development Team
