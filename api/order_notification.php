<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$nama = $data['nama'] ?? '';
$no_order = $data['no_order'] ?? '';
$status = $data['status'] ?? '';

if (empty($nama) || empty($no_order) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$prompt = "Buat pesan notifikasi WhatsApp yang ramah untuk customer toko online. 
Customer: $nama, No. Order: $no_order, Status: $status. 
Bahasa Indonesia, casual tapi profesional, max 100 kata, sertakan emoji yang relevan.";

require_once __DIR__ . '/../config/groq.php';

$response = callGroq($prompt, 300);

if (strpos($response, 'Error:') === false) {
    echo json_encode(['success' => true, 'message' => trim($response)]);
} else {
    echo json_encode(['success' => false, 'message' => 'API Error: ' . $response]);
}
