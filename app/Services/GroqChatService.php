<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqChatService implements \App\Contracts\ChatServiceInterface
{
    /**
     * Send chat messages to the Groq API.
     * Incorporates system prompts and returns the response string.
     */
    public function getChatResponse(array $messages): string
    {
        $apiKey = (string) config('groq.api_key');
        $model = config('groq.model', 'llama3-8b-8192');
        $url = (string) config('groq.api_url', 'https://api.groq.com/openai/v1/chat/completions');
        $maxTokens = config('groq.max_tokens', 512);
        $temperature = config('groq.temperature', 0.7);

        // Fallback check: If the API key is not configured or is a placeholder, use local fallback
        if (empty($apiKey) || str_starts_with($apiKey, 'your_') || str_starts_with($apiKey, 'your-') || $apiKey === 'fake_api_key_fallback') {
            Log::info("Groq API key not set or is placeholder. Using local fallback assistant.");
            return $this->localFallbackResponse($messages);
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(12)
                ->post($url, [
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? 'Maaf, saya tidak dapat memproses pesan Anda.';
            }

            Log::error("Groq API returned an error: " . $response->status() . " - " . $response->body());
            return $this->localFallbackResponse($messages);

        } catch (\Exception $e) {
            Log::error("Failed to connect to Groq AI API: " . $e->getMessage());
            return $this->localFallbackResponse($messages);
        }
    }

    /**
     * Return a generator that yields chunks of the response for Server-Sent Events (SSE).
     */
    public function streamChatResponse(array $messages): \Generator
    {
        $apiKey = (string) config('groq.api_key');
        $model = config('groq.model', 'llama3-8b-8192');
        $url = (string) config('groq.api_url', 'https://api.groq.com/openai/v1/chat/completions');
        $maxTokens = config('groq.max_tokens', 1024);
        $temperature = config('groq.temperature', 1.0);

        if (empty($apiKey) || str_starts_with($apiKey, 'your_') || str_starts_with($apiKey, 'your-')) {
            Log::info("Groq API key not set or is placeholder. Using local fallback assistant.");
            $fallback = $this->localFallbackResponse($messages);
            // Simulate streaming for fallback
            $words = explode(' ', $fallback);
            foreach ($words as $word) {
                yield $word . ' ';
                usleep(50000); // 50ms delay
            }
            return;
        }

        try {
            // Use a regular (non-stream option) HTTP client first to check connectivity,
            // then process the stream. This prevents crashes on 429/5xx errors.
            $response = Http::withToken($apiKey)
                ->withOptions(['stream' => true])
                ->timeout(30)
                ->post($url, [
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                    'top_p' => 1,
                    'stream' => true,
                    'stop' => null
                ]);

            // Check for API errors (429 Rate Limit, 401 Unauthorized, 5xx etc.)
            if (!$response->successful()) {
                $statusCode = $response->status();
                $errorBody = '';
                try { $errorBody = $response->body(); } catch (\Exception $e) { /* ignore */ }

                if ($statusCode === 429) {
                    Log::warning("Groq API rate limited (429). Falling back to local assistant.");
                } else {
                    Log::error("Groq API Streaming Error: {$statusCode} - {$errorBody}");
                }

                // Use local fallback with simulated streaming
                $fallback = $this->localFallbackResponse($messages);
                $words = explode(' ', $fallback);
                foreach ($words as $word) {
                    yield $word . ' ';
                    usleep(30000); // 30ms delay for natural feel
                }
                return;
            }

            $stream = $response->toPsrResponse()->getBody();
            $buffer = '';

            while (!$stream->eof()) {
                $chunk = $stream->read(1024);
                $buffer .= $chunk;
                
                while (($pos = strpos($buffer, "\n\n")) !== false) {
                    $line = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + 2);
                    
                    if (str_starts_with($line, 'data: ')) {
                        $data = substr($line, 6);
                        if ($data === '[DONE]') {
                            break 2;
                        }
                        
                        $decoded = json_decode($data, true);
                        if (isset($decoded['choices'][0]['delta']['content'])) {
                            yield $decoded['choices'][0]['delta']['content'];
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error("Failed to connect to Groq AI API for streaming: " . $e->getMessage());
            
            // Always provide a response to the user via local fallback
            $fallback = $this->localFallbackResponse($messages);
            $words = explode(' ', $fallback);
            foreach ($words as $word) {
                yield $word . ' ';
                usleep(30000);
            }
        }
    }

    /**
     * Rules-based local backup agent when Groq is offline or key is missing.
     * Guarantees that "Kiki" is always responsive and friendly.
     */
    protected function localFallbackResponse(array $messages): string
    {
        // Retrieve the last user message
        $userMsg = '';
        for ($i = count($messages) - 1; $i >= 0; $i--) {
            if ($messages[$i]['role'] === 'user') {
                $userMsg = strtolower($messages[$i]['content']);
                break;
            }
        }

        if (empty($userMsg)) {
            return "Halo! Ada yang bisa Kiki bantu hari ini? Kiki siap merekomendasikan produk atau menjawab pertanyaan seputar toko online TokoKu! 😊";
        }

        // 1. Greeting
        if (preg_match('/(halo|hai|pagi|siang|sore|malam|assalamualaikum|permisi)/', $userMsg)) {
            return "Halo! Saya Kiki, asisten virtual TokoKu. Kiki siap membantu Anda berbelanja produk premium, mengecek stok barang, atau membantu pelacakan pesanan Anda! Ada yang ingin ditanyakan? 😊";
        }

        // 2. Who is Kiki
        if (preg_match('/(siapa|kiki|nama|identitas|profil)/', $userMsg)) {
            return "Nama saya Kiki! Saya adalah asisten virtual resmi di TokoKu Store. Tugas saya adalah membantu merekomendasikan produk, menjelaskan cara pemesanan, dan memberikan solusi terbaik bagi pelanggan setia kami. 🤖✨";
        }

        // 3. Products and recommendations
        if (preg_match('/(rekomendasi|produk|terlaris|bagus|beli apa|jual apa)/', $userMsg)) {
            return "TokoKu memiliki banyak produk unggulan! Untuk produk elektronik terlaris, kami merekomendasikan **Keyboard Mechanical RGB** dan **Mouse Wireless Ergonomis** kami. Jika Anda mencari pakaian, kami punya **Jaket Bomber Navy Premium** yang sangat trendi! Silakan cari produk-produk tersebut di kolom pencarian toko kami ya. 😉";
        }

        // 4. How to buy
        if (preg_match('/(cara beli|cara pesan|checkout|belanja|cara bayar|pembayaran)/', $userMsg)) {
            return "Caranya sangat mudah! 🛒\n1. Pilih produk premium yang Anda inginkan di halaman Toko.\n2. Klik tombol **Beli** untuk memasukkannya ke Keranjang Belanja.\n3. Masuk ke halaman **Keranjang**, atur jumlah item, lalu klik **Checkout**.\n4. Isi alamat pengiriman Anda, pilih metode bayar (Tunai, Transfer Bank, atau QRIS), lalu konfirmasi pesanan Anda!\n\nApakah ada langkah yang masih membingungkan? Kiki siap pandu.";
        }

        // 5. Order tracking
        if (preg_match('/(lacak|status pesanan|order|kirim|resi)/', $userMsg)) {
            return "Untuk melacak pesanan Anda, Anda hanya perlu mengklik menu **Riwayat Pesanan** di bagian atas navigasi. Di sana, Anda bisa melihat status real-time pesanan Anda apakah *pending*, *paid* (lunas), atau *cancelled*. Anda juga bisa mencetak struk belanja PDF resmi dari sana! 📄📦";
        }

        // 6. Return policy
        if (preg_match('/(retur|kembali|tukar|garansi|rusak)/', $userMsg)) {
            return "Mohon maaf, demi menjaga keaslian dan higienitas produk, semua barang yang sudah dibeli di TokoKu tidak dapat ditukar atau dikembalikan, kecuali terdapat kesalahan pengiriman dari pihak kami yang disertai dengan bukti video unboxing lengkap. Anda dapat menghubungi admin resmi kami jika ada kendala ya. 🤝";
        }

        // 7. Shipping / location
        if (preg_match('/(alamat|lokasi|kirim dari|ongkir|ongkos kirim)/', $userMsg)) {
            return "Kantor pusat TokoKu berlokasi di **Jl. Merdeka Raya No. 45, Jakarta**. Semua paket pesanan dikirimkan dari gudang kami di Jakarta menggunakan kurir kilat. Biaya pengiriman akan otomatis dihitung saat Anda melakukan Checkout berdasarkan berat total produk! 🚚💨";
        }

        // Default response
        return "Pertanyaan menarik! Kiki sarankan Anda mencari produk impian Anda melalui kolom pencarian di halaman utama TokoKu. Anda juga bisa memasukkannya ke keranjang belanja Anda secara langsung. Apakah ada hal spesifik lain tentang produk atau pesanan yang ingin Kiki bantu? 😊";
    }
}
