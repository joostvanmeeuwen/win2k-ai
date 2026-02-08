<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Controller\Response;

final readonly class ModelCollection
{
    /** @param ModelItem[] $models */
    public function __construct(
        /** @var ModelItem[] */
        public array $models,
    ) {
    }
}
