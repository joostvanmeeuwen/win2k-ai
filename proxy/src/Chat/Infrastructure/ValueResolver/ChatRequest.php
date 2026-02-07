<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\ValueResolver;

final readonly class ChatRequest
{
    public function __construct(
        public string $prompt,
        public string $model,
        public bool $backInTime,
    ) {
    }
}
