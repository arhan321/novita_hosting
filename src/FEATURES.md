# Multi Base Engineering - Fitur Lengkap

## 🎨 Desain UI/UX

### Skema Warna
- **Navy Blue (#102a43)**: Warna utama yang mencerminkan profesionalisme dan engineering
- **Orange (#f97316)**: Warna aksen untuk Call-to-Action buttons
- **Abu-abu & Putih**: Warna sekunder untuk tampilan clean dan modern

### Komponen UI
- Navigasi dengan background navy dan hover effects orange
- Card design dengan shadow dan border yang elegan
- Button dengan transisi smooth
- Icon Font Awesome untuk visual yang lebih baik
- Responsive design untuk semua device

## 👥 User Roles

### 1. Customer (Pelanggan)
**Akses**: `/customer/*`

#### Fitur:
- **Dashboard**: Overview pesanan dan status
- **Katalog Produk**: Browse produk yang tersedia
- **Custom Order**: Pesan produk custom dengan spesifikasi sendiri
- **Manajemen Pesanan**: 
  - Lihat semua pesanan
  - Track status pesanan
  - Upload file desain
  - Pilih metode pengiriman
- **Pembayaran**: Upload bukti pembayaran

### 2. Admin
**Akses**: `/admin/*`

#### Fitur:
- **Dashboard**: Statistik pesanan dan pembayaran
- **Manajemen Pesanan**:
  - Verifikasi pesanan baru
  - Set harga untuk custom order
  - Update status pesanan
  - Kirim ke produksi
- **Manajemen Pembayaran**:
  - Verifikasi bukti pembayaran
  - Track pembayaran per pesanan
- **Manajemen Produk**:
  - CRUD produk katalog
  - Set harga dan spesifikasi
- **Dashboard Invoice**:
  - Lihat semua invoice
  - Filter dan search
  - Download PDF
  - Print invoice

### 3. Production (Tim Produksi)
**Akses**: `/production/*`

#### Fitur:
- **Dashboard**: Pesanan yang perlu diproduksi
- **Manajemen Produksi**:
  - Lihat detail pesanan produksi
  - Update progress produksi
  - Tandai pesanan selesai
  - Log aktivitas produksi

### 4. Owner (Pemilik) ⭐ NEW
**Akses**: `/owner/*`

#### Fitur:
- **Dashboard Owner**:
  - Total pendapatan (verified payments)
  - Total pesanan
  - Pesanan selesai
  - Pembayaran pending
  - Grafik pendapatan 6 bulan terakhir
  - Daftar pesanan terbaru

- **Laporan Keuangan**:
  - Filter berdasarkan periode tanggal
  - Total pemasukan
  - Rekap pesanan dan pesanan selesai
  - Pemasukan harian
  - Detail pembayaran lengkap
  - Export ke Excel/CSV

- **Invoice Management**:
  - Lihat semua invoice
  - Filter berdasarkan status, tanggal, customer
  - Search by order number atau nama
  - Download PDF invoice
  - Print invoice
  - Riwayat pembayaran per invoice

## 🚚 Sistem Pengiriman ⭐ NEW

### Opsi Pengiriman

#### 1. Ambil Sendiri (Pickup)
- **Biaya**: Rp 0
- **Lokasi**: Multi Base Engineering, Ruko Fiorenza, Citra Raya, Tangerang
- Customer datang langsung ke lokasi

#### 2. Jasa Pribadi (Internal Delivery)
- **Biaya**: GRATIS
- **Syarat**:
  - Minimal order: Rp 500.000
  - Maksimal jarak: 30 km dari lokasi perusahaan
- **Validasi**: Otomatis oleh sistem

#### 3. Per Kilometer
- **Tarif**: Rp 5.000 per km
- **Kalkulasi**: Otomatis berdasarkan jarak
- **Tanpa batas**: Minimal order atau jarak maksimal

### Fitur Geolocation

#### Auto-detect Lokasi
- Gunakan GPS device untuk mendapatkan lokasi saat ini
- Otomatis isi alamat dan koordinat
- Kalkulasi jarak real-time

#### Search Alamat
- Input alamat manual
- Search menggunakan Nominatim API (OpenStreetMap)
- Konversi alamat ke koordinat
- Kalkulasi jarak otomatis

#### Kalkulasi Jarak
- Menggunakan Haversine formula
- Akurat untuk jarak pendek-menengah
- Dari lokasi perusahaan: -6.1754, 106.5772
- Hasil dalam kilometer dengan 2 desimal

### Validasi Pengiriman
- Sistem otomatis validasi syarat jasa pribadi
- Warning jika tidak memenuhi syarat
- Estimasi biaya real-time
- Konfirmasi sebelum submit order

## 📄 Dashboard Invoice ⭐ NEW

### Fitur Invoice

#### List Invoice
- Tampilan tabel dengan informasi lengkap:
  - ID Pesanan
  - Nama Pelanggan & Email
  - Harga Produk
  - Biaya Pengiriman
  - Total Pembayaran
  - Status Pembayaran (Lunas/Belum Lunas)
  - Status Produksi

#### Filter & Search
- **Filter Status**: Pending, Verified, Paid, In Production, Completed
- **Filter Tanggal**: Range dari-sampai
- **Search**: By order number atau nama customer
- **Pagination**: 20 items per page

#### Detail Invoice
Informasi lengkap meliputi:
- Header perusahaan dengan logo
- Nomor invoice dan tanggal
- Informasi customer lengkap
- Detail produk dengan qty dan harga
- Informasi pengiriman (jika ada)
- Subtotal, biaya kirim, dan total
- Status pembayaran dan produksi
- Riwayat pembayaran

#### Download PDF
- Format profesional
- Header dengan informasi perusahaan
- Layout clean dan mudah dibaca
- Otomatis download dengan nama file: `invoice_{order_number}.pdf`

#### Print Invoice
- Optimized untuk print
- Button print otomatis
- Format tanpa navigasi dan footer
- Clean layout untuk kertas A4

## 💰 Laporan Keuangan (Owner) ⭐ NEW

### Dashboard Keuangan

#### Summary Cards
- **Total Pendapatan**: Semua pembayaran verified
- **Total Pesanan**: Jumlah semua pesanan
- **Pesanan Selesai**: Pesanan dengan status completed
- **Pembayaran Pending**: Pembayaran yang belum diverifikasi

#### Grafik Pendapatan
- Pendapatan 6 bulan terakhir
- Breakdown per bulan
- Format currency Indonesia

#### Filter Periode
- Pilih tanggal mulai dan akhir
- Default: Bulan berjalan
- Otomatis update data

### Detail Laporan

#### Pemasukan Harian
- Breakdown per hari
- Total pemasukan per hari
- Format tanggal Indonesia

#### Detail Pembayaran
- Tanggal dan waktu pembayaran
- Nomor order
- Nama pelanggan
- Jumlah pembayaran
- Metode pembayaran

### Export Data
- Format: CSV/Excel
- Nama file: `laporan_keuangan_{start_date}_to_{end_date}.csv`
- Include semua data dalam periode
- Compatible dengan Excel dan Google Sheets

## 🔄 Workflow Sistem

### Customer Order Flow
1. Customer browse katalog atau buat custom order
2. Pilih metode pengiriman
3. Input alamat (jika perlu pengiriman)
4. Sistem kalkulasi biaya pengiriman
5. Submit order
6. Upload bukti pembayaran
7. Track status pesanan

### Admin Verification Flow
1. Terima notifikasi pesanan baru
2. Review detail pesanan
3. Set harga (untuk custom order)
4. Verifikasi pesanan
5. Terima bukti pembayaran
6. Verifikasi pembayaran
7. Kirim ke produksi
8. Generate invoice

### Production Flow
1. Terima pesanan dari admin
2. Lihat detail dan spesifikasi
3. Update progress produksi
4. Log aktivitas
5. Tandai selesai

### Owner Monitoring Flow
1. Login ke dashboard owner
2. Lihat overview bisnis
3. Check laporan keuangan
4. Filter berdasarkan periode
5. Review invoice
6. Export data untuk analisis

## 🛠️ Technical Stack

### Backend
- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Database**: MySQL
- **PDF**: DomPDF

### Frontend
- **CSS**: Tailwind CSS
- **JavaScript**: Vanilla JS
- **Icons**: Font Awesome 6.5.1
- **Maps**: Nominatim API (OpenStreetMap)

### APIs
- **Geolocation**: Browser Geolocation API
- **Geocoding**: Nominatim (OpenStreetMap)
- **Distance**: Haversine Formula

## 📱 Responsive Design

### Breakpoints
- **Mobile**: < 640px
- **Tablet**: 640px - 1024px
- **Desktop**: > 1024px

### Mobile Features
- Hamburger menu
- Touch-friendly buttons
- Optimized forms
- Responsive tables
- Mobile-first design

## 🔒 Security

### Authentication
- Laravel built-in authentication
- Password hashing (bcrypt)
- CSRF protection
- Session management

### Authorization
- Role-based access control
- Policy-based permissions
- Middleware protection
- Route guards

### Data Protection
- Input validation
- SQL injection prevention
- XSS protection
- File upload validation

## 📊 Reporting

### Available Reports
1. **Laporan Keuangan** (Owner)
   - Total pendapatan
   - Breakdown per periode
   - Detail pembayaran

2. **Invoice** (Admin & Owner)
   - Per pesanan
   - PDF format
   - Print ready

3. **Production Log** (Production)
   - Progress tracking
   - Activity log

## 🚀 Performance

### Optimization
- Database indexing
- Eager loading relationships
- Query optimization
- Asset minification
- Caching strategy

### Loading Time
- Dashboard: < 2s
- Invoice generation: < 3s
- PDF download: < 5s
- Search/Filter: < 1s

## 📞 Support & Maintenance

### Regular Updates
- Security patches
- Bug fixes
- Feature enhancements
- Performance improvements

### Backup
- Daily database backup
- File storage backup
- Invoice archive

### Monitoring
- Error logging
- Performance monitoring
- User activity tracking

---

**Version**: 2.0.0  
**Last Updated**: April 25, 2026  
**Documentation**: Complete
