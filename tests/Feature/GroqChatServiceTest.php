<?php

namespace Tests\Feature;

use App\Services\GroqChatService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GroqChatServiceTest extends TestCase
{
    protected GroqChatService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        config(['groq.api_key' => 'fake_api_key']);
        config(['groq.model' => 'test-model']);
        
        $this->service = new GroqChatService();
    }

    public function test_it_returns_successful_sync_response()
    {
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Hello from fake Groq!'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->service->getChatResponse([['role' => 'user', 'content' => 'Hi']]);
        
        $this->assertEquals('Hello from fake Groq!', $response);
    }

    public function test_it_returns_fallback_response_on_api_error()
    {
        Http::fake([
            'api.groq.com/*' => Http::response(['error' => 'Rate limited'], 429)
        ]);

        $response = $this->service->getChatResponse([['role' => 'user', 'content' => 'Halo']]);
        
        $this->assertStringContainsString('Halo!', $response);
        $this->assertStringContainsString('Kiki', $response);
    }

    public function test_it_returns_stream_generator_successfully()
    {
        // Mock streaming response. 
        // In Laravel, simulating a stream with Http::fake is tricky directly via Generator, 
        // but since we will implement streaming using Http::withOptions([\'stream\' => true]),
        // we can test the fallback or mock the behavior. For true unit testing, we might need to mock the PSR-7 response body.
        
        // Simulating the stream lines (Server-Sent Events format typical of Groq/OpenAI)
        $streamContent = "data: {\"choices\": [{\"delta\": {\"content\": \"Hello \"}}]}\n\n"
                       . "data: {\"choices\": [{\"delta\": {\"content\": \"streaming \"}}]}\n\n"
                       . "data: {\"choices\": [{\"delta\": {\"content\": \"world!\"}}]}\n\n"
                       . "data: [DONE]\n\n";

        // In Http::fake, streaming can be mocked by returning a string containing the stream payloads.
        Http::fake([
            'api.groq.com/*' => Http::response($streamContent, 200)
        ]);

        $generator = $this->service->streamChatResponse([['role' => 'user', 'content' => 'Hi stream']]);
        
        $result = '';
        foreach ($generator as $chunk) {
            $result .= $chunk;
        }

        $this->assertEquals('Hello streaming world!', $result);
    }
}
