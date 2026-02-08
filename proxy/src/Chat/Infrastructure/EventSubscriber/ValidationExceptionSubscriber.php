<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\EventSubscriber;

use App\Chat\Infrastructure\Controller\Response\ErrorResponse;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener]
final readonly class ValidationExceptionSubscriber
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof UnprocessableEntityHttpException) {
            return;
        }

        $previous = $exception->getPrevious();
        if (!$previous instanceof ValidationFailedException) {
            return;
        }

        $violations = $previous->getViolations();
        $message = $violations->count() > 0
            ? (string) $violations->get(0)->getMessage()
            : 'Validation failed';

        $format = $event->getRequest()->getPreferredFormat('json');
        $mimeTypes = ['json' => 'application/json', 'xml' => 'application/xml'];

        $event->setResponse(new Response(
            $this->serializer->serialize(new ErrorResponse($message), $format),
            Response::HTTP_BAD_REQUEST,
            ['Content-Type' => $mimeTypes[$format] ?? 'application/json'],
        ));
    }
}
