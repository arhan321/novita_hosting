# PANDUAN PEMBAYARAN BERTAHAP - MULTIBASE ENGINEERING

## 🔄 ALUR LENGKAP PEMBAYARAN DP

### 📌 SCENARIO TEST: Order Katalog Rp 2.500.000 dengan DP Rp 1.000.000

---

## 1️⃣ CUSTOMER: Buat Pesanan

**Login:**
- Email: `customer@test.com`
- Password: `password`

**Langkah:**
1. Klik menu "**Katalog Produk**"
2. Pilih produk (misal: Plat Besi)
3. Klik "**Pesan Produk Ini**"
4. Isi quantity, catatan
5. Klik "**Buat Pesanan**"

**Hasil:**
- ✅ Status: **PENDING** (kuning)
- ⏸️ Tidak ada button pembayaran (menunggu admin)
- 📝 Order Number muncul (simpan ini!)

---

## 2️⃣ ADMIN: Verifikasi Pesanan & Set Harga

**Login:**
- Email: `admin@multibase.com`
- Password: `password`

**Langkah:**
1. Klik menu "**Pesanan**"
2. Cari pesanan dengan status **Pending**
3. Klik "**Detail**" pada pesanan tersebut
4. **DI SIDEBAR KANAN**, lihat section "**Update Status**"
5. Dropdown "Status" → pilih "**Verified**"
6. Klik "**Update Status**"

**Hasil:**
- ✅ Status berubah: **VERIFIED** (biru)
- ✅ Pesanan siap untuk dibayar

**⚠️ PENTING:** Untuk Custom Order, admin harus set harga dulu di panel "**Set Harga & Estimasi**" sebelum verify!

---

## 3️⃣ CUSTOMER: Upload Bukti Pembayaran DP

**Langkah:**
1. Refresh halaman atau kembali ke order detail
2. **HARUS MUNCUL** button biru besar: "**Upload Bukti Pembayaran**"
3. Klik button tersebut
4. Isi form:
   - Metode Pembayaran: **Transfer Bank**
   - **Tipe Pembayaran: DOWN PAYMENT (DP)** ⭐ PENTING!
   - **Jumlah Dibayar: 1000000** (1 juta dari 2.5 juta)
   - Upload gambar bukti transfer
   - Catatan: "DP terlebih dahulu"
5. Klik "**Upload Bukti Pembayaran**"

**Hasil:**
- ✅ Upload berhasil
- ✅ Muncul alert: "Bukti pembayaran berhasil diupload. Menunggu verifikasi admin."
- ⏸️ Button upload **HILANG**
- 🟡 Muncul box kuning: "**Pembayaran sedang menunggu verifikasi admin...**"

**Di section "Riwayat Pembayaran":**
- 📝 Pembayaran #1: Rp 1.000.000 (DP) - Status: **PENDING** (kuning)

---

## 4️⃣ ADMIN: Verifikasi Pembayaran DP ⭐ STEP KRUSIAL!

**Langkah:**
1. Klik menu "**Pembayaran**" di navigation bar
2. Anda akan melihat **LIST PEMBAYARAN**
3. Cari card dengan:
   - Order Number sesuai pesanan
   - Customer: customer@test.com
   - Amount: **Rp 1.000.000**
   - Status badge: **PENDING** (kuning)
   - **Tipe: DP** (badge biru kecil)
4. Di bagian bawah card, ada 2 button:
   - **Button HIJAU: "✓ Verifikasi"** ← KLIK INI!
   - Button MERAH: "✗ Tolak"
5. Klik "**Verifikasi**"
6. Confirm dialog muncul → Klik **OK**

**Hasil:**
- ✅ Redirect ke halaman pembayaran
- ✅ Muncul alert hijau: "**Pembayaran diverifikasi. Sisa tagihan: Rp 1.500.000**"
- ✅ Status payment berubah: **VERIFIED** (hijau)
- ✅ Order status TETAP: **VERIFIED** (belum PAID, karena belum lunas)

**🔍 CEK DATABASE (Optional - untuk debug):**
```bash
php artisan tinker
```
```php
$payment = \App\Models\Payment::find(3); // ganti 3 dengan ID payment
echo $payment->status; // harus "verified"
```

---

## 5️⃣ CUSTOMER: Cek Progress & Lanjutkan Pembayaran

**Langkah:**
1. Kembali ke halaman order detail (refresh browser!)
2. **HARUS TERLIHAT:**

   **📊 Ringkasan Pembayaran:**
   ```
   ┌─────────────────────────────────────────┐
   │ Total Tagihan: Rp 2.500.000            │
   │ Sudah Dibayar: Rp 1.000.000  ✅ HIJAU  │
   │ Sisa Tagihan:  Rp 1.500.000  ❌ MERAH  │
   └─────────────────────────────────────────┘
   ```

   **📋 Riwayat Pembayaran:**
   ```
   ┌─────────────────────────────────────────┐
   │ Pembayaran #1                           │
   │ Rp 1.000.000 (DP) - VERIFIED ✅         │
   │ Transfer Bank | 08 Apr 2026 17:05       │
   └─────────────────────────────────────────┘
   ```

   **🔵 BUTTON MUNCUL LAGI:**
   ```
   [💳 Lanjutkan Pembayaran (Sisa: Rp 1.500.000)]
   ```

3. **⚠️ JIKA "Sudah Dibayar" MASIH Rp 0:**
   - Admin belum klik "Verifikasi" di step 4
   - Atau masih ada cache, jalankan:
     ```bash
     php artisan config:clear
     php artisan cache:clear
     php artisan view:clear
     ```
   - Hard refresh browser: `Ctrl + Shift + R` (Windows) atau `Cmd + Shift + R` (Mac)

---

## 6️⃣ CUSTOMER: Cicilan ke-2 (Optional)

**Langkah:**
1. Klik "**Lanjutkan Pembayaran**"
2. Isi form:
   - Metode: Transfer Bank
   - **Tipe: CICILAN**
   - **Jumlah: 1000000** (1 juta lagi)
   - Upload bukti
3. Submit

**Hasil:**
- Payment baru dengan status: **PENDING**
- Menunggu admin verifikasi lagi

---

## 7️⃣ ADMIN: Verifikasi Cicilan

**Sama seperti step 4:**
1. Menu "**Pembayaran**"
2. Cari payment baru (Amount: Rp 1.000.000, Tipe: Cicilan)
3. Klik "**Verifikasi**"

**Hasil:**
- Alert: "**Sisa tagihan: Rp 500.000**"
- Order masih status: **VERIFIED**

---

## 8️⃣ CUSTOMER: Pelunasan

**Langkah:**
1. Klik "**Lanjutkan Pembayaran**"
2. Isi form:
   - Metode: Transfer Bank
   - **Tipe: LUNAS / PELUNASAN**
   - **Jumlah: 500000** (sisa terakhir)
   - Upload bukti
3. Submit

---

## 9️⃣ ADMIN: Verifikasi Pelunasan

**Langkah:**
1. Menu "**Pembayaran**"
2. Verifikasi payment terakhir

**Hasil:**
- ✅ Alert: "**Pembayaran diverifikasi. Pesanan sudah LUNAS.**"
- ✅ Order status otomatis berubah: **PAID** (indigo)
- ✅ Button "**Kirim ke Produksi**" muncul di detail order

---

## 🔟 CUSTOMER: Cek Status Final

**Halaman Order:**
- 📊 **Ringkasan Pembayaran:**
  - Sudah Dibayar: **Rp 2.500.000** ✅
  - Sisa: **Rp 0** ✅
  - Badge: **"Pembayaran LUNAS"** (hijau)
- 📋 **Riwayat Pembayaran:** 3 pembayaran semua VERIFIED
- 🔒 Button "Lanjutkan Pembayaran" **HILANG**
- 💼 Pesan: "Pembayaran Anda sudah dikonfirmasi"

---

## 🛠️ TROUBLESHOOTING

### ❌ Problem: "Sudah Dibayar" tetap Rp 0

**Diagnosis:**
```bash
php artisan tinker
```
```php
$payment = \App\Models\Payment::find(X); // ganti X
echo $payment->status; // cek statusnya apa
```

**Solusi berdasarkan output:**

1. **Jika status = "pending":**
   - ⚠️ Admin **BELUM klik "Verifikasi"**
   - Ikuti step 4 lagi dengan teliti

2. **Jika status = "verified" tapi tetap Rp 0:**
   - Clear cache:
     ```bash
     php artisan config:clear
     php artisan cache:clear
     php artisan view:clear
     ```
   - Restart PHP server
   - Hard refresh browser

3. **Jika payment tidak ada di database:**
   - Upload payment gagal
   - Cek `storage/logs/laravel.log`

---

### ❌ Problem: Button "Upload Bukti Pembayaran" tidak muncul

**Kemungkinan penyebab:**

1. **Order status bukan "verified":**
   - Solution: Admin harus ubah status ke "verified" dulu (step 2)

2. **Masih ada payment pending:**
   - Solution: Admin verify payment sebelumnya dulu

3. **Order belum punya harga:**
   - Solution: Admin set harga dulu (untuk custom order)

---

### ❌ Problem: Button "Verifikasi" tidak ada di halaman admin

**Kemungkinan:**

1. **Payment sudah verified:**
   - Cek status badge, jika hijau = sudah verified

2. **Menu "Pembayaran" tidak muncul:**
   - URL manual: `http://localhost:8000/admin/payments`

---

## 📸 SCREENSHOT LOKASI PENTING

### Admin Menu Navigation:
```
[Dashboard] [Pesanan] [Pembayaran] [Produk] [Logout]
                          ↑
                    KLIK DISINI!
```

### Admin Payments Page - Button Location:
```
╔════════════════════════════════════════════╗
║ Payment Card                               ║
║ Order: ORD202604089343                    ║
║ Customer: customer@test.com               ║
║ Amount: Rp 1.000.000 (DP) [PENDING]      ║
║                                           ║
║ [🗑️ Lihat Bukti]                          ║
║                                           ║
║ ┌─────────────┐  ┌──────────┐           ║
║ │ ✓ Verifikasi│  │ ✗ Tolak  │           ║
║ └─────────────┘  └──────────┘           ║
║      ↑ KLIK INI!                         ║
╚════════════════════════════════════════════╝
```

---

## 🎯 CHECKLIST KUNCI

Sebelum komplain "tidak work", pastikan:

- [ ] Admin sudah **LOGIN** sebagai admin
- [ ] Admin sudah **BUKA MENU "PEMBAYARAN"**
- [ ] Payment ada di list dengan status **PENDING**
- [ ] Admin sudah **KLIK BUTTON HIJAU "VERIFIKASI"**
- [ ] Muncul alert **"Pembayaran diverifikasi. Sisa tagihan..."**
- [ ] Customer sudah **HARD REFRESH** browser (Ctrl+Shift+R)
- [ ] Cache sudah di-clear jika perlu

---

## 📞 DEBUG COMMANDS

Jika masih bermasalah, jalankan:

```bash
# Check payment status
php artisan tinker
```

```php
// Payment specific
$payment = \App\Models\Payment::find(3);
echo "Status: " . $payment->status . "\n";
echo "Order ID: " . $payment->order_id . "\n";

// Order & calculations
$order = \App\Models\Order::find(3);
echo "Total Price: " . $order->total_price . "\n";
echo "Total Paid: " . $order->total_paid . "\n";
echo "Remaining: " . $order->remaining_balance . "\n";
echo "Status: " . $order->status . "\n";
```

Expected output setelah verify:
```
Status: verified
Total Paid: 1000000
Remaining: 1500000
```

---

## ✅ KESIMPULAN

Pembayaran bertahap **SUDAH BERFUNGSI** jika:
1. ✅ Admin bisa klik "Verifikasi" di menu Pembayaran
2. ✅ Payment status berubah dari pending → verified
3. ✅ Customer melihat "Sudah Dibayar" bertambah
4. ✅ Button "Lanjutkan Pembayaran" muncul untuk cicilan
5. ✅ Order status jadi "paid" setelah lunas

**⚠️ Yang PENTING:** Admin **HARUS** verifikasi setiap pembayaran di menu "**Pembayaran**", bukan cuma ubah status order!

---

**Last Updated:** 9 April 2026
**Version:** 1.0
