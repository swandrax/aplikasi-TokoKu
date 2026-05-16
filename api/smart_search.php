<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$query = $input['query'] ?? '';

if (empty($query)) {
    echo json_encode(['success' => false, 'message' => 'Query is empty']);
    exit;
}

require_once __DIR__ . '/../config/groq.php';

$prompt = "Ekstrak parameter pencarian dari query ini: '$query'. Return ONLY valid JSON: {\"keywords\": [], \"max_price\": number|null, \"category\": string|null}. Do not include markdown formatting or any other text.";

$response = callGroq($prompt, 500);

if (strpos($response, 'Error:') === false) {
    // Attempt to extract JSON if there's markdown wrapper
    $content = preg_replace('/```json|```/', '', $response);
    $parsed = json_decode(trim($content), true);
    
    if ($parsed) {
        echo json_encode(['success' => true, 'data' => $parsed]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to parse JSON', 'raw' => $content]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'API Request Failed', 'response' => $response]);
}
