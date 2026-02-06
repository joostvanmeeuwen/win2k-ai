<?php

declare(strict_types=1);

namespace App\Chat\Domain\Model;

final readonly class ChatResponse
{
    public function __construct(
        public string $response,
        public string $model,
    ) {
    }
}
