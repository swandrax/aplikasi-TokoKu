<?php

return [
    'api_key'  => env('GROQ_API_KEY'),
    'model'    => env('GROQ_MODEL', 'llama3-8b-8192'),
    'api_url'  => env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions'),
    'max_tokens' => 512,
    'temperature' => 0.7,
];
