<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Controller;

use App\Chat\Application\Command\SendChatCommand;
use App\Chat\Application\Command\SendChatCommandHandler;
use App\Chat\Application\Query\GetModelsQuery;
use App\Chat\Application\Query\GetModelsQueryHandler;
use App\Chat\Infrastructure\Controller\Response\ChatResponse;
use App\Chat\Infrastructure\Controller\Response\ErrorResponse;
use App\Chat\Infrastructure\Controller\Response\ModelItem;
use App\Chat\Infrastructure\Controller\Response\ModelCollection;
use App\Chat\Infrastructure\Controller\ValueResolver\ChatRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ChatController
{
    public function __construct(
        private SendChatCommandHandler $sendChatHandler,
        private GetModelsQueryHandler $getModelsHandler,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('/api/chat', name: 'api_chat', methods: ['POST'])]
    public function chat(ChatRequest $chatRequest, Request $request): Response
    {
        $format = $request->getPreferredFormat('json');

        $errors = $this->validator->validate($chatRequest);
        if (count($errors) > 0) {
            return $this->respond(
                new ErrorResponse((string)$errors->get(0)->getMessage()),
                $format,
                400,
            );
        }

        try {
            $command = new SendChatCommand(
                prompt: $chatRequest->prompt,
                model: $chatRequest->model,
                backInTime: $chatRequest->backInTime,
            );

            $response = ($this->sendChatHandler)($command);

            return $this->respond(
                new ChatResponse($response->response, $response->model),
                $format,
            );
        } catch (\Throwable $e) {
            return $this->respond(
                new ErrorResponse($e->getMessage()),
                $format,
                500,
            );
        }
    }

    #[Route('/api/models', name: 'api_models', methods: ['GET'])]
    public function models(Request $request): Response
    {
        $format = $request->getPreferredFormat('json');
        $models = ($this->getModelsHandler)(new GetModelsQuery());

        $items = array_map(
            fn($model) => new ModelItem($model->id, $model->name, $model->provider),
            $models,
        );

        return $this->respond(new ModelCollection($items), $format);
    }

    private function respond(object $data, string $format, int $status = 200): Response
    {
        $mimeTypes = ['json' => 'application/json', 'xml' => 'application/xml'];

        return new Response(
            $this->serializer->serialize($data, $format),
            $status,
            ['Content-Type' => $mimeTypes[$format] ?? 'application/json'],
        );
    }
}
