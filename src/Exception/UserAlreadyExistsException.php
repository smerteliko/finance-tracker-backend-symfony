<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserAlreadyExistsException extends HttpException
{
    public function __construct(string $message = 'User with this email already exists.', \Throwable $previous = null, array $headers = [], int $code = 0)
    {
        parent::__construct(Response::HTTP_CONFLICT, $message, $previous, $headers, $code);
    }
}
