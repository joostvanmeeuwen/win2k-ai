<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Controller;

use App\Chat\Application\Command\SendChatCommand;
use App\Chat\Application\Command\SendChatCommandHandler;
use App\Chat\Application\Query\GetModelsQuery;
use App\Chat\Application\Query\GetModelsQueryHandler;
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
    public function chat(Request $request): Response
    {
        $contentType = $request->headers->get('Content-Type', '');
        $accept = $request->headers->get('Accept', 'text/plain');

        // Parse input based on Content-Type
        if (str_contains($contentType, 'application/json')) {
            $data = json_decode($request->getContent(), true) ?? [];
            $prompt = $data['prompt'] ?? '';
            $model = $data['model'] ?? '';
            $backInTime = (bool) ($data['back_in_time'] ?? false);
        } else {
            // Default: form-urlencoded
            $prompt = $request->request->get('prompt', '');
            $model = $request->request->get('model', '');
            $backInTime = (bool) $request->request->get('back_in_time', false);
        }

        if (empty($prompt)) {
            return $this->errorResponse('Prompt is required', $accept, 400);
        }

        if (empty($model)) {
            return $this->errorResponse('Model is required', $accept, 400);
        }

        try {
            $command = new SendChatCommand(
                prompt: $prompt,
                model: $model,
                backInTime: $backInTime,
            );

            $response = ($this->sendChatHandler)($command);

            // Return based on Accept header
            if (str_contains($accept, 'application/json')) {
                return new JsonResponse([
                    'response' => $response->response,
                    'model' => $response->model,
                ]);
            }

            // Default: plain text for Windows 2000
            return new Response($response->response, 200, [
                'Content-Type' => 'text/plain; charset=utf-8',
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), $accept, 500);
        }
    }

    #[Route('/api/models', name: 'api_models', methods: ['GET'])]
    public function models(): JsonResponse
    {
        $models = ($this->getModelsHandler)(new GetModelsQuery());

        return new JsonResponse([
            'models' => array_map(fn($model) => $model->toArray(), $models),
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
