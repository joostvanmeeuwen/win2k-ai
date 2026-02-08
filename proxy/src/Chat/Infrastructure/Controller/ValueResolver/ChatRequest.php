<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Controller\ValueResolver;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ChatRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Prompt is required')]
        public string $prompt,
        #[Assert\NotBlank(message: 'Model is required')]
        public string $model,
        public bool $backInTime,
    ) {
    }
}
