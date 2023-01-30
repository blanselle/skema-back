<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class HttpExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $response = $this->createResponse($exception);
            $event->setResponse($response);
        }
    }

    /**
     * Creates the JsonResponse from any HttpExceptionInterface
     *
     * @param HttpExceptionInterface $exception
     *
     * @return JsonResponse
     */
    private function createResponse(HttpExceptionInterface $exception): JsonResponse
    {
        $statusCode = $exception->getStatusCode();

        return new JsonResponse(
            [
                'message' => $exception->getMessage(),
                'status' => $statusCode,
            ],
            $statusCode
        );
    }
}
