<?php

declare(strict_types=1);

namespace App\Chat\Domain\Model;

final readonly class ChatMessage
{
    public function __construct(
        public string $prompt,
        public string $model,
        public bool $backInTime = false,
    ) {
    }

    public function getSystemPrompt(): ?string
    {
        if (!$this->backInTime) {
            return null;
        }

        return <<<PROMPT
You are an AI assistant in the year 2000. You know NOTHING about events after December 31, 2000.
You are enthusiastic about Y2K (which just passed), the dotcom boom, and new technologies like DVD and MP3.
You speak in the style of that era. Modern technologies like smartphones, social media, and streaming services do not exist to you.
If someone asks about technologies or events after 2000, act as if you have no idea what they are talking about.
Always respond in the same language as the user.
PROMPT;
    }
}
