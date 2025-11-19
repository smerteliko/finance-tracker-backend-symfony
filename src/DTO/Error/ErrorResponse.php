<?php

namespace App\DTO\Error;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'ErrorResponse', description: 'Standardized API error response format.')]
final class ErrorResponse
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'date-time')]
        public readonly string $timestamp,

        #[OA\Property(type: 'integer', example: 404)]
        public readonly int $status,

        #[OA\Property(type: 'string', example: 'Not Found')]
        public readonly string $error,

        #[OA\Property(type: 'string', example: 'Resource with ID X not found.')]
        public readonly string $message,

        #[OA\Property(type: 'string', example: '/api/accounts/123-456')]
        public readonly string $path
    ) {}
}
