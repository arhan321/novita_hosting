<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\KnowledgeBase;
use App\Models\ChatSetting;
use App\Models\Message;
use App\Models\Product;

class BotService
{
    /**
     * Category keyword mapping for priority matching
     */
    private array $categoryKeywords = [
        'order'       => ['pesan', 'order', 'pesanan', 'beli', 'status', 'cancel', 'batal', 'produksi', 'selesai', 'po', 'purchase'],
        'pembayaran'  => ['bayar', 'payment', 'transfer', 'harga', 'biaya', 'cicil', 'dp', 'lunas', 'invoice', 'tagihan'],
        'produk'      => ['produk', 'product', 'katalog', 'custom', 'spesifikasi', 'material', 'bahan', 'ukuran', 'ada', 'tersedia', 'stok', 'stock'],
        'pengiriman'  => ['kirim', 'pengiriman', 'delivery', 'ongkir', 'jarak', 'pickup', 'ambil'],
    ];

    /**
     * Keywords that indicate a product availability check intent
     */
    private array $productCheckKeywords = [
        'ada', 'tersedia', 'stok', 'stock', 'punya', 'jual', 'menjual',
        'apakah ada', 'ada tidak', 'ada gak', 'ada ga', 'cek produk',
        'cari produk', 'produk apa', 'barang', 'item',
    ];

    /**
     * Keywords that indicate a PO / order creation intent
     */
    private array $orderIntentKeywords = [
        'pesan', 'order', 'beli', 'mau pesan', 'ingin pesan', 'mau beli',
        'ingin beli', 'buat pesanan', 'buat order', 'po', 'purchase order',
        'mau order', 'mau po', 'bikin pesanan',
    ];

    /**
     * Detect if the message is asking about product availability.
     * Returns the search query string or null.
     */
    public function detectProductCheckIntent(string $message): ?string
    {
        $normalized = mb_strtolower(trim($message));

        $hasProductKeyword = false;
        foreach ($this->productCheckKeywords as $kw) {
            if (str_contains($normalized, $kw)) {
                $hasProductKeyword = true;
                break;
            }
        }

        if (!$hasProductKeyword) {
            return null;
        }

        // Extract the search term by stripping common question words
        $stopPhrases = [
            'apakah ada', 'ada tidak', 'ada gak', 'ada ga', 'ada nggak',
            'apakah', 'ada', 'tersedia', 'stok', 'stock', 'punya', 'jual',
            'menjual', 'cek produk', 'cari produk', 'produk apa', 'barang',
            'item', 'produk', '?', 'ya', 'dong', 'kah',
        ];

        $query = $normalized;
        foreach ($stopPhrases as $phrase) {
            $query = str_replace($phrase, ' ', $query);
        }
        $query = trim(preg_replace('/\s+/', ' ', $query));

        return strlen($query) >= 2 ? $query : null;
    }

    /**
     * Detect if the message is requesting to place an order.
     * Returns the product search term or null.
     */
    public function detectOrderIntent(string $message): ?string
    {
        $normalized = mb_strtolower(trim($message));

        $hasOrderKeyword = false;
        foreach ($this->orderIntentKeywords as $kw) {
            if (str_contains($normalized, $kw)) {
                $hasOrderKeyword = true;
                break;
            }
        }

        if (!$hasOrderKeyword) {
            return null;
        }

        // Extract product name from the message
        $stopPhrases = [
            'mau pesan', 'ingin pesan', 'mau beli', 'ingin beli',
            'buat pesanan', 'buat order', 'purchase order', 'mau order',
            'mau po', 'bikin pesanan', 'pesan', 'order', 'beli', 'po',
            'saya', 'aku', 'dong', 'ya', 'kah', '?',
        ];

        $query = $normalized;
        foreach ($stopPhrases as $phrase) {
            $query = str_replace($phrase, ' ', $query);
        }
        $query = trim(preg_replace('/\s+/', ' ', $query));

        return strlen($query) >= 2 ? $query : null;
    }

    /**
     * Search products by name/category/material.
     * Returns array of matching products (max 5).
     */
    public function searchProducts(string $query): array
    {
        return Product::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%")
                  ->orWhere('material', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Build a bot reply for product availability check.
     */
    public function buildProductCheckReply(string $query): string
    {
        $products = $this->searchProducts($query);

        if (empty($products)) {
            return "Maaf, saya tidak menemukan produk yang cocok dengan \"*{$query}*\". "
                . "Silakan cek katalog lengkap kami di halaman Katalog Produk, atau hubungi admin untuk informasi lebih lanjut.";
        }

        $lines = ["Berikut produk yang tersedia untuk pencarian \"*{$query}*\":"];
        foreach ($products as $p) {
            $available = $p['is_available'] ? '✅ Tersedia' : '❌ Tidak tersedia';
            $price = $p['price'] ? 'Rp ' . number_format($p['price'], 0, ',', '.') : 'Harga by request';
            $lines[] = "• *{$p['name']}* — {$price} ({$available})";
            if (!empty($p['material'])) {
                $lines[] = "  Material: {$p['material']}";
            }
        }

        $lines[] = "\nIngin memesan salah satu produk di atas? Ketik \"pesan [nama produk]\" atau kunjungi halaman Katalog Produk.";

        return implode("\n", $lines);
    }

    /**
     * Build a bot reply for order intent.
     */
    public function buildOrderIntentReply(string $query): string
    {
        $products = $this->searchProducts($query);

        if (empty($products)) {
            return "Saya tidak menemukan produk \"*{$query}*\" di katalog kami. "
                . "Jika Anda ingin memesan produk custom, silakan kunjungi halaman *Custom Produk*. "
                . "Atau ketik nama produk yang Anda cari untuk saya bantu cek ketersediaannya.";
        }

        // Single product found — give direct order link
        if (count($products) === 1) {
            $p = $products[0];
            $available = $p['is_available'];
            $price = $p['price'] ? 'Rp ' . number_format($p['price'], 0, ',', '.') : 'Harga by request';

            if (!$available) {
                return "Produk *{$p['name']}* saat ini tidak tersedia untuk dipesan. "
                    . "Silakan hubungi admin untuk informasi lebih lanjut.";
            }

            $orderUrl = url("/customer/orders/catalog/{$p['id']}");
            return "Produk *{$p['name']}* tersedia dengan harga {$price}.\n\n"
                . "Untuk melanjutkan pemesanan, silakan klik link berikut:\n"
                . "🛒 {$orderUrl}\n\n"
                . "Atau kunjungi halaman Katalog Produk dan pilih produk tersebut.";
        }

        // Multiple products found
        $lines = ["Saya menemukan beberapa produk yang cocok dengan \"*{$query}*\":"];
        foreach ($products as $p) {
            $available = $p['is_available'] ? '✅' : '❌';
            $price = $p['price'] ? 'Rp ' . number_format($p['price'], 0, ',', '.') : 'By request';
            $lines[] = "• {$available} *{$p['name']}* — {$price}";
        }
        $lines[] = "\nSilakan kunjungi halaman Katalog Produk untuk memesan, atau sebutkan nama produk yang lebih spesifik.";

        return implode("\n", $lines);
    }

    /**
     * Process a customer message and return a bot response.
     * Returns null if confidence is below threshold (needs admin).
     */
    public function respond(Conversation $conversation, string $userMessage): ?string
    {
        // 1. Check for product availability intent
        $productQuery = $this->detectProductCheckIntent($userMessage);
        if ($productQuery !== null) {
            return $this->buildProductCheckReply($productQuery);
        }

        // 2. Check for order/PO intent
        $orderQuery = $this->detectOrderIntent($userMessage);
        if ($orderQuery !== null) {
            return $this->buildOrderIntentReply($orderQuery);
        }

        // 3. Fall back to knowledge base matching
        $entries = KnowledgeBase::active()->get();

        if ($entries->isEmpty()) {
            return null;
        }

        $threshold = (float) ChatSetting::get('confidence_threshold', '0.4');
        $priorityCategory = $this->detectCategory($userMessage);

        $bestScore = 0.0;
        $bestAnswer = null;

        foreach ($entries as $entry) {
            $score = $this->calculateConfidence($userMessage, $entry->question, $entry->category, $priorityCategory);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestAnswer = $entry->answer;
            }
        }

        if ($bestScore >= $threshold && $bestAnswer !== null) {
            return $bestAnswer;
        }

        return null;
    }

    /**
     * Calculate confidence score between user message and a KB entry.
     */
    private function calculateConfidence(string $userMessage, string $question, string $entryCategory, ?string $priorityCategory): float
    {
        $userWords = $this->tokenize($userMessage);
        $questionWords = $this->tokenize($question);

        if (empty($userWords) || empty($questionWords)) {
            return 0.0;
        }

        // Word overlap score
        $intersection = array_intersect($userWords, $questionWords);
        $union = array_unique(array_merge($userWords, $questionWords));
        $jaccardScore = count($intersection) / count($union);

        // Bigram overlap bonus
        $userBigrams = $this->getBigrams($userWords);
        $questionBigrams = $this->getBigrams($questionWords);
        $bigramIntersection = array_intersect($userBigrams, $questionBigrams);
        $bigramBonus = count($bigramIntersection) > 0 ? 0.1 : 0.0;

        // Category priority bonus
        $categoryBonus = ($priorityCategory && $entryCategory === $priorityCategory) ? 0.15 : 0.0;

        // Substring match bonus (user message contains key words from question)
        $substringBonus = 0.0;
        $normalizedUser = mb_strtolower($userMessage);
        $normalizedQuestion = mb_strtolower($question);
        if (str_contains($normalizedUser, $normalizedQuestion) || str_contains($normalizedQuestion, $normalizedUser)) {
            $substringBonus = 0.2;
        }

        $score = min(1.0, $jaccardScore + $bigramBonus + $categoryBonus + $substringBonus);

        return $score;
    }

    /**
     * Detect the most likely category from user message keywords.
     */
    private function detectCategory(string $message): ?string
    {
        $normalized = mb_strtolower($message);
        $bestCategory = null;
        $bestCount = 0;

        foreach ($this->categoryKeywords as $category => $keywords) {
            $count = 0;
            foreach ($keywords as $keyword) {
                if (str_contains($normalized, $keyword)) {
                    $count++;
                }
            }
            if ($count > $bestCount) {
                $bestCount = $count;
                $bestCategory = $category;
            }
        }

        return $bestCount > 0 ? $bestCategory : null;
    }

    /**
     * Tokenize a string into lowercase words, removing stopwords.
     */
    private function tokenize(string $text): array
    {
        $stopwords = ['yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'dengan', 'ini', 'itu',
            'ada', 'adalah', 'atau', 'juga', 'saya', 'anda', 'kamu', 'kami', 'kita',
            'bisa', 'dapat', 'akan', 'sudah', 'belum', 'tidak', 'bukan', 'apakah',
            'bagaimana', 'berapa', 'kapan', 'dimana', 'siapa', 'apa', 'cara', 'mau',
            'ingin', 'tolong', 'mohon', 'halo', 'hai', 'selamat', 'pagi', 'siang', 'malam'];

        $text = mb_strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/u', ' ', $text);
        $words = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_filter($words, fn($w) => !in_array($w, $stopwords) && strlen($w) > 2));
    }

    /**
     * Generate bigrams from a word array.
     */
    private function getBigrams(array $words): array
    {
        $bigrams = [];
        for ($i = 0; $i < count($words) - 1; $i++) {
            $bigrams[] = $words[$i] . '_' . $words[$i + 1];
        }
        return $bigrams;
    }

    /**
     * Get the configured fallback message.
     */
    public function getFallbackMessage(): string
    {
        return ChatSetting::get(
            'fallback_message',
            'Maaf, saya tidak dapat menjawab pertanyaan Anda secara otomatis. Pertanyaan Anda telah diteruskan kepada admin kami. Mohon tunggu sebentar.'
        );
    }
}
