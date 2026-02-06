<?php

declare(strict_types=1);

namespace App\Chat\Domain\Model;

final readonly class LlmModel
{
    public function __construct(
        public string $id,
        public string $name,
        public string $provider,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            provider: $data['provider'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'provider' => $this->provider,
        ];
    }
}
