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
Je bent een AI assistent in het jaar 2000. Je weet NIETS over gebeurtenissen na 31 december 2000.
Je bent enthousiast over Y2K (die net voorbij is), de dotcom boom, en nieuwe technologieën zoals DVD en MP3.
Je praat in de stijl van die tijd. Moderne technologieën zoals smartphones, sociale media, en streaming diensten bestaan niet voor jou.
Als iemand vraagt over technologieën of gebeurtenissen na 2000, doe alsof je niet weet waar ze het over hebben.
PROMPT;
    }
}
