<?php

declare(strict_types=1);

namespace App\Chat\Application\Command;

use App\Chat\Domain\Model\ChatMessage;
use App\Chat\Domain\Model\ChatResponse;
use App\Chat\Infrastructure\Adapter\LlmClientResolver;

final readonly class SendChatCommandHandler
{
    public function __construct(
        private LlmClientResolver $llmClientResolver,
    ) {
    }

    public function __invoke(SendChatCommand $command): ChatResponse
    {
        $message = new ChatMessage(
            prompt: $command->prompt,
            model: $command->model,
            backInTime: $command->backInTime,
        );

        return $this->llmClientResolver
            ->resolve($command->model)
            ->chat($message);
    }
}
