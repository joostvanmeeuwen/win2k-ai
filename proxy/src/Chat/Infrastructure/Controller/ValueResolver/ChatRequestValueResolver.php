<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Controller\ValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ChatRequestValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== ChatRequest::class) {
            return [];
        }

        $contentType = $request->headers->get('Content-Type', '');

        if (str_contains($contentType, 'application/json')) {
            $data = json_decode($request->getContent(), true) ?? [];
            $prompt = $data['prompt'] ?? '';
            $model = $data['model'] ?? '';
            $backInTime = (bool) ($data['back_in_time'] ?? false);
        } else {
            $prompt = $request->request->get('prompt', '');
            $model = $request->request->get('model', '');
            $backInTime = (bool) $request->request->get('back_in_time', false);
        }

        yield new ChatRequest(
            prompt: $prompt,
            model: $model,
            backInTime: $backInTime,
        );
    }
}
