<?php

declare(strict_types=1);

namespace App\Chat\Application\Command;

final readonly class SendChatCommand
{
    public function __construct(
        public string $prompt,
        public string $model,
        public bool $backInTime = false,
    ) {
    }
}
