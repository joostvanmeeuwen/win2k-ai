<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Adapter;

use App\Chat\Domain\Llm\LlmClientInterface;

final readonly class LlmClientResolver
{
    /** @param iterable<LlmClientInterface> $clients */
    public function __construct(
        private iterable $clients,
    ) {
    }

    public function resolve(string $model): LlmClientInterface
    {
        foreach ($this->clients as $client) {
            if ($client->supports($model)) {
                return $client;
            }
        }

        throw new \InvalidArgumentException(sprintf('No client found for model: %s', $model));
    }
}
