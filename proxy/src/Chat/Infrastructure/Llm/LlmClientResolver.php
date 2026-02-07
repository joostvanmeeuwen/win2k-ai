<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Llm;

use App\Chat\Domain\Llm\LlmClientInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class LlmClientResolver
{
    /** @param iterable<LlmClientInterface> $clients */
    public function __construct(
        #[AutowireIterator('app.llm_client')]
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
