<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KnowledgeBase;
use App\Models\ChatSetting;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // Default confidence threshold
        ChatSetting::updateOrCreate(
            ['key' => 'confidence_threshold'],
            ['value' => '0.4']
        );

        ChatSetting::updateOrCreate(
            ['key' => 'fallback_message'],
            ['value' => 'Maaf, saya tidak dapat menjawab pertanyaan Anda secara otomatis. Pertanyaan Anda telah diteruskan kepada admin kami. Mohon tunggu sebentar, admin akan segera membantu Anda.']
        );

        // Seed knowledge base
        $entries = [
            // Order
            [
                'question' => 'Bagaimana cara memesan produk?',
                'answer' => 'Anda dapat memesan produk melalui menu "Katalog Produk" untuk produk yang tersedia, atau menu "Custom Produk" untuk pesanan khusus sesuai kebutuhan Anda. Setelah memilih produk, ikuti langkah-langkah pemesanan yang tersedia.',
                'category' => 'order',
            ],
            [
                'question' => 'Bagaimana cara cek status pesanan saya?',
                'answer' => 'Anda dapat melihat status pesanan melalui menu "Pesanan Saya" di dashboard. Status pesanan meliputi: Pending (menunggu verifikasi), Verified (sudah diverifikasi), Paid (pembayaran dikonfirmasi), In Production (sedang diproduksi), dan Completed (selesai).',
                'category' => 'order',
            ],
            [
                'question' => 'Berapa lama proses produksi?',
                'answer' => 'Estimasi waktu produksi tergantung pada jenis dan kompleksitas pesanan. Setelah pesanan diverifikasi, admin akan memberikan estimasi tanggal penyelesaian. Anda dapat melihat estimasi tersebut di detail pesanan Anda.',
                'category' => 'order',
            ],
            [
                'question' => 'Apakah bisa membatalkan pesanan?',
                'answer' => 'Pembatalan pesanan dapat dilakukan sebelum pesanan masuk ke tahap produksi. Silakan hubungi admin kami melalui chat ini untuk proses pembatalan. Pesanan yang sudah dalam tahap produksi tidak dapat dibatalkan.',
                'category' => 'order',
            ],
            // Payment
            [
                'question' => 'Metode pembayaran apa saja yang tersedia?',
                'answer' => 'Kami menerima pembayaran melalui transfer bank. Setelah melakukan transfer, Anda perlu mengunggah bukti pembayaran melalui menu pembayaran di detail pesanan Anda untuk diverifikasi oleh admin.',
                'category' => 'pembayaran',
            ],
            [
                'question' => 'Bagaimana cara melakukan pembayaran?',
                'answer' => 'Untuk melakukan pembayaran: 1) Buka detail pesanan Anda, 2) Klik tombol "Bayar", 3) Pilih metode pembayaran, 4) Lakukan transfer sesuai nominal, 5) Upload bukti transfer. Admin akan memverifikasi pembayaran Anda dalam 1x24 jam.',
                'category' => 'pembayaran',
            ],
            [
                'question' => 'Berapa lama verifikasi pembayaran?',
                'answer' => 'Verifikasi pembayaran biasanya dilakukan dalam 1x24 jam pada hari kerja. Jika pembayaran Anda belum diverifikasi setelah 24 jam, silakan hubungi admin melalui chat ini.',
                'category' => 'pembayaran',
            ],
            [
                'question' => 'Apakah bisa bayar cicilan?',
                'answer' => 'Saat ini kami belum menyediakan opsi pembayaran cicilan. Pembayaran dilakukan secara penuh sebelum proses produksi dimulai. Untuk informasi lebih lanjut, silakan hubungi admin kami.',
                'category' => 'pembayaran',
            ],
            // Product
            [
                'question' => 'Produk apa saja yang tersedia?',
                'answer' => 'Kami menyediakan berbagai produk engineering dan manufaktur. Anda dapat melihat katalog lengkap produk kami di menu "Katalog Produk". Selain produk katalog, kami juga menerima pesanan custom sesuai spesifikasi Anda.',
                'category' => 'produk',
            ],
            [
                'question' => 'Apakah bisa pesan produk custom?',
                'answer' => 'Ya, kami menerima pesanan produk custom. Silakan gunakan menu "Custom Produk" dan isi detail spesifikasi yang Anda inginkan. Tim kami akan menghubungi Anda untuk konfirmasi dan penawaran harga.',
                'category' => 'produk',
            ],
            [
                'question' => 'Berapa harga produk?',
                'answer' => 'Harga produk katalog dapat dilihat langsung di halaman Katalog Produk. Untuk produk custom, harga akan ditentukan setelah admin meninjau spesifikasi pesanan Anda. Kami akan memberikan penawaran harga yang kompetitif.',
                'category' => 'produk',
            ],
            // Shipping
            [
                'question' => 'Apakah ada layanan pengiriman?',
                'answer' => 'Ya, kami menyediakan beberapa opsi pengiriman: 1) Ambil sendiri (pickup) - gratis, 2) Jasa pengiriman internal kami untuk area tertentu, 3) Pengiriman per km untuk jarak yang lebih jauh. Detail biaya pengiriman akan ditampilkan saat proses pemesanan.',
                'category' => 'pengiriman',
            ],
            [
                'question' => 'Berapa biaya pengiriman?',
                'answer' => 'Biaya pengiriman tergantung metode yang dipilih: Pickup gratis, pengiriman internal gratis untuk order minimal Rp 500.000 dalam radius 30 km, atau Rp 5.000/km untuk pengiriman per kilometer. Biaya akan dihitung otomatis saat pemesanan.',
                'category' => 'pengiriman',
            ],
            [
                'question' => 'Berapa jangkauan pengiriman?',
                'answer' => 'Layanan pengiriman internal kami menjangkau radius hingga 30 km dari lokasi kami. Untuk jarak lebih dari 30 km, tersedia opsi pengiriman per km atau Anda dapat mengambil pesanan langsung di tempat kami.',
                'category' => 'pengiriman',
            ],
            // General
            [
                'question' => 'Jam operasional',
                'answer' => 'Kami beroperasi pada hari Senin - Sabtu, pukul 08.00 - 17.00 WIB. Di luar jam operasional, Anda tetap dapat mengirim pesan dan kami akan merespons pada hari kerja berikutnya.',
                'category' => 'umum',
            ],
            [
                'question' => 'Bagaimana cara menghubungi admin?',
                'answer' => 'Anda dapat menghubungi admin melalui fitur chat ini. Ketik pesan Anda dan admin kami akan merespons secepatnya pada jam operasional (Senin-Sabtu, 08.00-17.00 WIB).',
                'category' => 'umum',
            ],
            [
                'question' => 'Apakah ada garansi produk?',
                'answer' => 'Kami memberikan garansi kualitas untuk setiap produk yang kami buat. Jika terdapat cacat produksi, silakan hubungi kami dalam 7 hari setelah produk diterima. Kami akan menangani klaim garansi dengan sebaik-baiknya.',
                'category' => 'umum',
            ],
        ];

        foreach ($entries as $entry) {
            KnowledgeBase::create($entry);
        }
    }
}
