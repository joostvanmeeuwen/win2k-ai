<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Adapter;

use App\Chat\Domain\Model\ChatMessage;
use App\Chat\Domain\Model\ChatResponse;
use App\Chat\Domain\Llm\LlmClientInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GeminiLlmClient implements LlmClientInterface
{
    private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiKey,
    ) {
    }

    public function chat(ChatMessage $message): ChatResponse
    {
        $url = sprintf(self::API_URL, $message->model) . '?key=' . $this->apiKey;

        $contents = [];

        $systemPrompt = $message->getSystemPrompt();
        if ($systemPrompt !== null) {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $systemPrompt]],
            ];
            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => 'Begrepen! Ik ben nu een AI assistent in het jaar 2000.']],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message->prompt]],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'contents' => $contents,
            ],
        ]);

        $data = $response->toArray(false);

        if (isset($data['error'])) {
            throw new \RuntimeException($data['error']['message'] ?? 'Unknown API error');
        }

        $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        return new ChatResponse(
            response: $responseText,
            model: $message->model,
        );
    }

    public function supports(string $model): bool
    {
        return str_starts_with($model, 'gemini');
    }
}
