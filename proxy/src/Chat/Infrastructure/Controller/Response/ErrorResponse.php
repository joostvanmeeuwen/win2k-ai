<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Controller\Response;

final readonly class ErrorResponse
{
    public function __construct(
        public string $error,
    ) {
    }
}
