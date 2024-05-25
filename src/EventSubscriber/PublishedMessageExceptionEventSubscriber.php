<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exceptions\InvalidFilterException;
use App\Exceptions\InvalidOperatorException;
use Carbon\Exceptions\InvalidFormatException;
use Doctrine\ORM\Query\QueryException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class PublishedMessageExceptionEventSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        $code = match (get_class($throwable)) {
            InvalidOperatorException::class, InvalidFilterException::class, QueryException::class, InvalidFormatException::class, BadRequestHttpException::class => Response::HTTP_BAD_REQUEST,
            NotFoundHttpException::class => Response::HTTP_NOT_FOUND,
            HttpException::class => $throwable->getStatusCode(),
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };

        $response_data = [
            'error' => [
                'code' => $code,
                'message' => $throwable->getMessage(),
            ],
        ];

        $event->setResponse(new JsonResponse($response_data, $code));
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }
}
