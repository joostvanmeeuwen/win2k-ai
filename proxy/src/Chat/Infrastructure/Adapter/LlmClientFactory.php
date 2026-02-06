<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Adapter;

use App\Chat\Domain\Model\ChatMessage;
use App\Chat\Domain\Model\ChatResponse;
use App\Chat\Domain\Llm\LlmClientInterface;

final readonly class LlmClientFactory implements LlmClientInterface
{
    /** @param array<array{id: string, name: string, provider: string}> $models */
    public function __construct(
        private ClaudeLlmClient $claudeClient,
        private GeminiLlmClient $geminiClient,
        private array $models,
    ) {
    }

    public function chat(ChatMessage $message): ChatResponse
    {
        $client = $this->getClientForModel($message->model);

        return $client->chat($message);
    }

    public function supports(string $model): bool
    {
        return $this->claudeClient->supports($model) || $this->geminiClient->supports($model);
    }

    private function getClientForModel(string $model): LlmClientInterface
    {
        $provider = $this->getProviderForModel($model);

        return match ($provider) {
            'anthropic' => $this->claudeClient,
            'google' => $this->geminiClient,
            default => throw new \InvalidArgumentException(sprintf('Unsupported model: %s', $model)),
        };
    }

    private function getProviderForModel(string $model): ?string
    {
        foreach ($this->models as $modelConfig) {
            if ($modelConfig['id'] === $model) {
                return $modelConfig['provider'];
            }
        }

        // Fallback to prefix-based detection
        if (str_starts_with($model, 'claude')) {
            return 'anthropic';
        }
        if (str_starts_with($model, 'gemini')) {
            return 'google';
        }

        return null;
    }
}
