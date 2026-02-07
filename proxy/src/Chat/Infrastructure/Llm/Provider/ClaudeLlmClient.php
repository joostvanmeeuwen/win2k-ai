<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Llm\Provider;

use App\Chat\Domain\Model\ChatMessage;
use App\Chat\Domain\Model\ChatResponse;
use App\Chat\Domain\Llm\LlmClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ClaudeLlmClient implements LlmClientInterface
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const API_VERSION = '2023-06-01';

    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire(env: 'ANTHROPIC_API_KEY')]
        private string $apiKey,
    ) {
    }

    public function chat(ChatMessage $message): ChatResponse
    {
        $requestBody = [
            'model' => $message->model,
            'max_tokens' => 4096,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $message->prompt,
                ],
            ],
        ];

        $requestBody['system'] = $message->getSystemPrompt();

        $response = $this->httpClient->request('POST', self::API_URL, [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-api-key' => $this->apiKey,
                'anthropic-version' => self::API_VERSION,
            ],
            'json' => $requestBody,
        ]);

        $data = $response->toArray(false);

        if (isset($data['error'])) {
            throw new \RuntimeException($data['error']['message'] ?? 'Unknown API error');
        }

        $responseText = '';
        foreach ($data['content'] as $block) {
            if ($block['type'] === 'text') {
                $responseText .= $block['text'];
            }
        }

        return new ChatResponse(
            response: $responseText,
            model: $message->model,
        );
    }

    public function supports(string $model): bool
    {
        return str_starts_with($model, 'claude');
    }
}
