<?php
if (!defined('GROQ_API_KEY')) define('GROQ_API_KEY', env('GROQ_API_KEY', getenv('GROQ_API_KEY') ?: 'YOUR_GROQ_API_KEY'));
if (!defined('GROQ_MODEL')) define('GROQ_MODEL', 'qwen/qwen3-32b');
if (!defined('GROQ_ENDPOINT')) define('GROQ_ENDPOINT', 'https://api.groq.com/openai/v1/chat/completions');

if (!function_exists('callGroq')) {
    function callGroq(string $prompt, int $max_tokens = 750): string {
        $data = [
            'model' => GROQ_MODEL,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'max_tokens' => $max_tokens,
            'temperature' => 0.7
        ];

        $ch = curl_init(GROQ_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . GROQ_API_KEY
            ]
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? 'Error: Groq tidak merespons.';
    }
}
?>
