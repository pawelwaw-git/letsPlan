<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Contracts\PublishedMessageException;
use App\Contracts\UserInputException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class PublishedMessageExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof PublishedMessageException) {
            return;
        }

        $code = $throwable instanceof UserInputException ? Response::HTTP_BAD_REQUEST : Response::HTTP_INTERNAL_SERVER_ERROR;

        $response_data = [
            'error' => [
                'code' => $code,
                'message' => $throwable->getMessage(),
            ],
        ];

        $event->setResponse(new JsonResponse($response_data,$code));
    }
}