<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$type = $data['type'] ?? '';
$summary_data = $data['summary_data'] ?? [];

$prompt = "";

if ($type === 'penjualan') {
    $periode = $summary_data['periode'] ?? 'Semua';
    $n = $summary_data['total_order'] ?? 0;
    $total = number_format($summary_data['revenue'] ?? 0, 0, ',', '.');
    $cancelled = $summary_data['cancelled'] ?? 0;
    $top_produk = $summary_data['top_produk'] ?? 'N/A';
    $top_customer = $summary_data['top_customer'] ?? 'N/A';
    
    $prompt = "Buat ringkasan eksekutif laporan penjualan toko online: 
    Periode: $periode, Total Order: $n, Revenue: Rp $total, 
    Order Cancelled: $cancelled, Produk Terlaris: $top_produk, 
    Customer Terbanyak Beli: $top_customer. 
    Format: paragraf eksekutif 3-4 kalimat + 3 poin rekomendasi. Bahasa Indonesia formal.";
} elseif ($type === 'produk') {
    $total_produk = $summary_data['total_produk'] ?? 0;
    $nilai_stok = number_format($summary_data['nilai_stok'] ?? 0, 0, ',', '.');
    
    $prompt = "Buat ringkasan eksekutif laporan produk toko online:
    Total Produk Aktif: $total_produk, Total Nilai Stok: Rp $nilai_stok.
    Format: paragraf eksekutif 3-4 kalimat + 3 poin rekomendasi manajemen inventori. Bahasa Indonesia formal.";
} else {
    echo json_encode(['success' => false, 'message' => 'Tipe laporan tidak valid']);
    exit;
}

require_once __DIR__ . '/../config/groq.php';

$response = callGroq($prompt, 500);

if (strpos($response, 'Error:') === false) {
    echo json_encode(['success' => true, 'message' => trim($response)]);
} else {
    echo json_encode(['success' => false, 'message' => 'API Error: ' . $response]);
}
