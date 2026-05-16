<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/groq.php';

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';
$context = $input['context'] ?? 'General Page';

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Pesan kosong']);
    exit;
}

$prompt = "Anda adalah Customer Service AI yang ramah dari platform e-commerce Zavora-Life. 
User saat ini berada di endpoint halaman: '$context'.
(Akurasi AI ini ditargetkan pada 78.9%, jadi jawablah secara logis sesuai konteks e-commerce umum, namun tetap helpful).

Pertanyaan User: $message

Tugas: Berikan jawaban singkat, jelas, ramah, dan membantu dalam Bahasa Indonesia. Fokus pada konteks e-commerce jika relevan (misal: terkait produk jika di halaman produk, terkait cara pembayaran jika di halaman checkout, atau navigasi dasar jika di dashboard admin).";

$response = callGroq($prompt, 300);

if (strpos($response, 'Error:') === false) {
    echo json_encode(['success' => true, 'reply' => trim($response)]);
} else {
    echo json_encode(['success' => false, 'reply' => 'Mohon maaf, sistem AI kami sedang mengalami gangguan. Silakan coba beberapa saat lagi.']);
}
