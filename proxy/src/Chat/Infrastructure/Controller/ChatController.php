<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Controller;

use App\Chat\Application\Command\SendChatCommand;
use App\Chat\Application\Command\SendChatCommandHandler;
use App\Chat\Application\Query\GetModelsQuery;
use App\Chat\Application\Query\GetModelsQueryHandler;
use App\Chat\Infrastructure\ValueResolver\ChatRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class ChatController
{
    public function __construct(
        private SendChatCommandHandler $sendChatHandler,
        private GetModelsQueryHandler $getModelsHandler,
    ) {
    }

    #[Route('/api/chat', name: 'api_chat', methods: ['POST'])]
    public function chat(ChatRequest $chatRequest, Request $request): Response
    {
        $accept = $request->headers->get('Accept', 'text/plain');

        if (empty($chatRequest->prompt)) {
            return $this->errorResponse('Prompt is required', $accept, 400);
        }

        if (empty($chatRequest->model)) {
            return $this->errorResponse('Model is required', $accept, 400);
        }

        try {
            $command = new SendChatCommand(
                prompt: $chatRequest->prompt,
                model: $chatRequest->model,
                backInTime: $chatRequest->backInTime,
            );

            $response = ($this->sendChatHandler)($command);

            if (str_contains($accept, 'application/json')) {
                return new JsonResponse([
                    'response' => $response->response,
                    'model' => $response->model,
                ]);
            }

            return new Response($response->response, 200, [
                'Content-Type' => 'text/plain; charset=utf-8',
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), $accept, 500);
        }
    }

    #[Route('/api/models', name: 'api_models', methods: ['GET'])]
    public function models(Request $request): Response
    {
        $accept = $request->headers->get('Accept', 'text/plain');
        $models = ($this->getModelsHandler)(new GetModelsQuery());

        if (str_contains($accept, 'application/json')) {
            return new JsonResponse([
                'models' => array_map(fn($model) => $model->toArray(), $models),
            ]);
        }

        $lines = array_map(
            fn($model) => sprintf('%s|%s|%s', $model->id, $model->name, $model->provider),
            $models
        );

        return new Response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    private function errorResponse(string $message, string $accept, int $statusCode): Response
    {
        if (str_contains($accept, 'application/json')) {
            return new JsonResponse(['error' => $message], $statusCode);
        }

        return new Response('Error: ' . $message, $statusCode, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
