<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Controller\Response;

final readonly class ModelItem
{
    public function __construct(
        public string $id,
        public string $name,
        public string $provider,
    ) {
    }
}
