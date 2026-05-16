<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$revenue = $data['revenue'] ?? 0;
$growth = $data['growth'] ?? 0;
$produk = $data['produk'] ?? '';
$n_order = $data['n_order'] ?? 0;

$prompt = "Kamu adalah analis bisnis untuk toko online. Analisis data berikut:
Revenue bulan ini: Rp " . number_format($revenue, 0, ',', '.') . ", Growth: " . number_format($growth, 1) . "%,
Top produk: $produk, Total order: $n_order.
Berikan: 1) Analisis performa, 2) Tren yang terlihat, 3) Rekomendasi strategi. 
Bahasa Indonesia, format bullet point.";

require_once __DIR__ . '/../config/groq.php';

$response = callGroq($prompt, 500);

if (strpos($response, 'Error:') === false) {
    echo json_encode(['success' => true, 'message' => trim($response)]);
} else {
    echo json_encode(['success' => false, 'message' => 'API Error: ' . $response]);
}
