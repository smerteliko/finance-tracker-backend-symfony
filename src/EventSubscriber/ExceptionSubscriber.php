<?php

namespace App\EventSubscriber;

use App\DTO\Error\ErrorResponse;
use App\Exception\ResourceNotFoundException;
use App\Exception\UserAlreadyExistsException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Catches exceptions and converts them into standardized JSON error responses.
 */
final class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $path = $event->getRequest()->getPathInfo();

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $errorName = 'Internal Server Error';
        $message = 'An unexpected error occurred.';

        if ($exception instanceof ResourceNotFoundException) {
            $statusCode = Response::HTTP_NOT_FOUND;
            $errorName = 'Not Found';
            $message = $exception->getMessage();
        } elseif ($exception instanceof UserAlreadyExistsException) {
            $statusCode = Response::HTTP_CONFLICT; // 409 Conflict
            $errorName = 'Conflict';
            $message = $exception->getMessage();
        } elseif ($exception instanceof AccessDeniedException || $exception instanceof AccessDeniedHttpException) {
            $statusCode = Response::HTTP_FORBIDDEN; // 403 Forbidden
            $errorName = 'Forbidden';
            $message = 'Access Denied. You do not have permission to access this resource.';
        } elseif ($exception instanceof \InvalidArgumentException) {
            $statusCode = Response::HTTP_BAD_REQUEST; // 400 Bad Request
            $errorName = 'Bad Request';
            $message = $exception->getMessage();
        }
        elseif ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $errorName = Response::$statusTexts[$statusCode] ?? 'Error';
            $message = $exception->getMessage();
        }

        if ($statusCode === Response::HTTP_INTERNAL_SERVER_ERROR) {
            if ($_ENV['APP_ENV'] !== 'dev') {
                $message = 'An unexpected error occurred. Please try again later.';
            } else {
                $message = $exception->getMessage();
            }
        }

        $errorResponse = new ErrorResponse(
            timestamp: (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
            status: $statusCode,
            error: $errorName,
            message: $message,
            path: $path
        );

        $response = new JsonResponse($errorResponse, $statusCode);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => ['onKernelException', 10],
        ];
    }
}
