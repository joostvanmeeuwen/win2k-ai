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
