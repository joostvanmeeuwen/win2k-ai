<?php

declare(strict_types=1);

namespace App\Chat\Domain\Llm;

use App\Chat\Domain\Model\ChatMessage;
use App\Chat\Domain\Model\ChatResponse;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.llm_client')]
interface LlmClientInterface
{
    public function chat(ChatMessage $message): ChatResponse;

    public function supports(string $model): bool;
}
