<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Llm\Provider;

use App\Chat\Domain\Model\ChatMessage;
use App\Chat\Domain\Model\ChatResponse;
use App\Chat\Domain\Llm\LlmClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GeminiLlmClient implements LlmClientInterface
{
    private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';

    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire(env: 'GOOGLE_API_KEY')]
        private string $apiKey,
    ) {
    }

    public function chat(ChatMessage $message): ChatResponse
    {
        $url = sprintf(self::API_URL, $message->model) . '?key=' . $this->apiKey;

        $requestBody = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $message->prompt]],
                ],
            ],
        ];

        $requestBody['systemInstruction'] = [
            'parts' => [['text' => $message->getSystemPrompt()]],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $requestBody,
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
