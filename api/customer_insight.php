<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$nama = $input['nama'] ?? 'Customer';
$n = $input['n'] ?? 0;
$total = $input['total'] ?? 0;
$produk = $input['produk'] ?? 'Belum ada';

require_once __DIR__ . '/../config/groq.php';

$prompt = "Berikan insight singkat tentang profil customer ini untuk admin toko:
Nama: $nama, Total Order: $n, Total Belanja: Rp$total, Produk sering dibeli: $produk. 
Saran dalam Bahasa Indonesia, 3 poin bullet, actionable.";

$response = callGroq($prompt, 300);

if (strpos($response, 'Error:') === false) {
    $insight = trim($response);
    echo json_encode(['success' => true, 'insight' => $insight]);
} else {
    echo json_encode(['success' => false, 'message' => 'API Request Failed', 'response' => $response]);
}
