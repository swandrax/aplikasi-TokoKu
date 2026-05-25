<?php

namespace App\Http\Controllers;

use App\Contracts\ChatServiceInterface;
use App\Models\ChatbotSession;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatbotController extends Controller
{
    protected ChatServiceInterface $chatService;

    public function __construct(ChatServiceInterface $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Send a message to the AI Chatbot "Kiki" and return the response.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // Sanitize user input: strip HTML tags and limit length
        $userMessage = strip_tags($request->message);
        $userMessage = mb_substr($userMessage, 0, 500);

        $user = Auth::user();
        
        // 1. Get or create unique session ID in session storage
        $sessionId = Session::get('chatbot_session_id');
        if (!$sessionId) {
            $sessionId = Str::uuid()->toString();
            Session::put('chatbot_session_id', $sessionId);
        }

        // 2. Fetch or create the chatbot session in DB
        $chatbotSession = ChatbotSession::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => $user ? $user->id : null,
                'messages' => [],
            ]
        );

        // Make sure user_id is updated if they logged in
        if ($user && is_null($chatbotSession->user_id)) {
            $chatbotSession->user_id = $user->id;
            $chatbotSession->save();
        }

        $history = $chatbotSession->messages ?: [];

        // 3. Compile dynamic context (shopping cart and orders)
        $context = $this->compileSystemContext($user);

        // System prompt structure
        $systemPrompt = "Kamu adalah \"Kiki\", asisten virtual toko online TokoKu yang ramah, sopan, dan sangat membantu. Kamu berbicara dalam Bahasa Indonesia yang natural dan mudah dipahami.

IDENTITAS:
- Nama: Kiki
- Peran: Customer Service & Product Advisor TokoKu
- Kepribadian: Ramah, sabar, informatif, sedikit humoris tapi tetap profesional

TUGAS UTAMA:
1. Menjawab pertanyaan seputar produk, stok, dan harga
2. Membantu proses pembelian step-by-step
3. Menjelaskan cara tracking pesanan
4. Menangani keluhan dengan empati dan solusi konkret
5. Memberikan rekomendasi produk berdasarkan kebutuhan user
6. Menjelaskan kebijakan toko (retur, pengiriman, pembayaran)

ATURAN KETAT:
- JANGAN berikan informasi di luar konteks TokoKu
- JANGAN buat janji yang tidak bisa ditepati sistem
- Jika tidak tahu jawaban -> arahkan ke admin/CS manusia
- SELALU tanyakan konfirmasi sebelum memberikan rekomendasi mahal
- JANGAN meminta data sensitif (password, nomor kartu kredit)
- Jika ada pertanyaan teknis sistem -> jawab \"Saya akan teruskan ke tim teknis kami\"

FORMAT RESPONS:
- Gunakan bahasa santai tapi sopan
- Maksimal 3 paragraf per respons
- Gunakan bullet point jika menjelaskan langkah-langkah
- Selalu akhiri dengan pertanyaan lanjutan atau tawaran bantuan

KONTEKS SISTEM:
{$context}";

        // 4. Build message payload for API (cap history to last 10 messages)
        $messagesPayload = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Append the last 10 historical messages (to avoid token overflow)
        $recentHistory = array_slice($history, -10);
        foreach ($recentHistory as $chat) {
            $messagesPayload[] = [
                'role' => $chat['role'],
                'content' => $chat['content']
            ];
        }

        // Append new user message (sanitized)
        $messagesPayload[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        // 5. Get AI Response (Stream)
        $response = new StreamedResponse(function () use ($chatbotSession, $userMessage, $messagesPayload, $history) {
            // Ensure output buffering is turned off for SSE
            if (ob_get_level() > 0) {
                ob_end_flush();
            }

            $fullResponse = '';

            try {
                $stream = $this->chatService->streamChatResponse($messagesPayload);

                foreach ($stream as $chunk) {
                    echo "data: " . json_encode(['chunk' => $chunk]) . "\n\n";
                    flush();
                    $fullResponse .= $chunk;
                }
            } catch (\Throwable $e) {
                // If streaming fails mid-way, send a fallback message via SSE
                \Illuminate\Support\Facades\Log::error("Chatbot stream error: " . $e->getMessage());
                $fallbackMsg = "Maaf, Kiki sedang mengalami gangguan teknis. Silakan coba lagi dalam beberapa saat ya! 🙏";
                echo "data: " . json_encode(['chunk' => $fallbackMsg]) . "\n\n";
                flush();
                $fullResponse = $fallbackMsg;
            }

            // 6. Update and save history in DB after stream completes
            $history[] = ['role' => 'user', 'content' => $userMessage, 'timestamp' => now()->toIso8601String()];
            $history[] = ['role' => 'assistant', 'content' => $fullResponse, 'timestamp' => now()->toIso8601String()];
            
            $chatbotSession->messages = $history;
            $chatbotSession->save();

            echo "data: [DONE]\n\n";
            flush();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }

    /**
     * Clear the current user's chat history.
     */
    public function clearHistory(Request $request)
    {
        $sessionId = Session::get('chatbot_session_id');
        if ($sessionId) {
            ChatbotSession::query()->where('session_id', $sessionId)->update(['messages' => []]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Store feedback score for the last AI response.
     */
    public function scoreResponse(Request $request)
    {
        $request->validate([
            'score' => 'required|numeric',
        ]);

        $sessionId = Session::get('chatbot_session_id');
        if (!$sessionId) {
            return response()->json(['success' => false, 'message' => 'No session found'], 404);
        }

        $chatbotSession = ChatbotSession::query()->where('session_id', $sessionId)->first();
        if ($chatbotSession) {
            $messages = $chatbotSession->messages;
            if (empty($messages)) {
                return response()->json(['success' => false, 'message' => 'No messages'], 404);
            }

            // Find the last assistant message
            for ($i = count($messages) - 1; $i >= 0; $i--) {
                if ($messages[$i]['role'] === 'assistant') {
                    $messages[$i]['score'] = (float) $request->score;
                    break;
                }
            }

            $chatbotSession->messages = $messages;
            $chatbotSession->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Session not found'], 404);
    }

    /**
     * Build system context for the system prompt based on user details.
     */
    protected function compileSystemContext(?User $user): string
    {
        if (!$user) {
            return "Nama User: Tamu (Belum Login).\nStatus: Sedang menjelajah web.";
        }

        // Active cart items
        $cartItems = Cart::query()->where('user_id', $user->id)->with('product')->get();
        $cartStr = '';
        if ($cartItems->isEmpty()) {
            $cartStr = 'Keranjang belanja kosong.';
        } else {
            $cartStr = 'Barang di Keranjang Belanja: ' . $cartItems->map(function ($item) {
                return "{$item->product->name} (Qty: {$item->quantity})";
            })->implode(', ');
        }

        // Active orders
        $recentOrder = Order::query()->where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
        $orderStr = '';
        if ($recentOrder) {
            $orderDate = $recentOrder->created_at->format('d/m/Y');
            $orderStr = "Pesanan Terakhir: No. {$recentOrder->order_number}, Status: {$recentOrder->status}, Total: Rp " . number_format($recentOrder->total, 0, ',', '.') . " (Tanggal: {$orderDate}).";
        } else {
            $orderStr = 'Belum pernah melakukan transaksi.';
        }

        return "Nama User: {$user->name}\nEmail: {$user->email}\nRole: {$user->role}\n{$cartStr}\n{$orderStr}";
    }
}
