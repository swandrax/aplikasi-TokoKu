<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Jakarta');

$input = json_decode(file_get_contents('php://input'), true);
$nama = $input['nama'] ?? 'Admin';
$n = $input['n'] ?? 0;
$jam = date('H:i');

require_once __DIR__ . '/../config/groq.php';

$prompt = "Buat sapaan motivasi singkat untuk admin toko bernama $nama, jam $jam, ada $n order hari ini. Bahasa Indonesia, casual, 1-2 kalimat. Jangan beri formatting markdown atau teks tambahan.";

$response = callGroq($prompt, 100);

if (strpos($response, 'Error:') === false) {
    // Clean up if it outputs quotes
    $greeting = trim($response, '"');
    
    echo json_encode(['success' => true, 'greeting' => $greeting]);
} else {
    echo json_encode(['success' => false, 'message' => 'API Request Failed', 'response' => $response]);
}
