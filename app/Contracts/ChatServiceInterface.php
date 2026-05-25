<?php

namespace App\Contracts;

interface ChatServiceInterface
{
    /**
     * Get a complete response string (synchronous).
     *
     * @param array $messages
     * @return string
     */
    public function getChatResponse(array $messages): string;

    /**
     * Return a generator that yields chunks of the response.
     * This is useful for Server-Sent Events (SSE) streaming.
     *
     * @param array $messages
     * @return \Generator
     */
    public function streamChatResponse(array $messages): \Generator;
}
