<?php

declare(strict_types=1);

namespace App\Chat\Application\Query;

use App\Chat\Domain\Model\LlmModel;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class GetModelsQueryHandler
{
    /** @param array<array{id: string, name: string, provider: string}> $models */
    public function __construct(
        #[Autowire(param: 'llm.models')]
        private array $models,
    ) {
    }

    /** @return LlmModel[] */
    public function __invoke(GetModelsQuery $query): array
    {
        return array_map(
            fn(array $model) => LlmModel::fromArray($model),
            $this->models
        );
    }
}
