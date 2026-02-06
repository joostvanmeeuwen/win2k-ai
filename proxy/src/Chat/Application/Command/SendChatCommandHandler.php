<?php

declare(strict_types=1);

namespace App\Chat\Application\Command;

use App\Chat\Domain\Model\ChatMessage;
use App\Chat\Domain\Model\ChatResponse;
use App\Chat\Domain\Llm\LlmClientInterface;

final readonly class SendChatCommandHandler
{
    public function __construct(
        private LlmClientInterface $llmClient,
    ) {
    }

    public function __invoke(SendChatCommand $command): ChatResponse
    {
        $message = new ChatMessage(
            prompt: $command->prompt,
            model: $command->model,
            backInTime: $command->backInTime,
        );

        return $this->llmClient->chat($message);
    }
}
