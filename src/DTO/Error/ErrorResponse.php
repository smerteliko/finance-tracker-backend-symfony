<?php

namespace App\DTO\Error;

use OpenApi\Attributes as OA;

class ErrorResponse
{
    public function __construct(
        #[OA\Property(description: 'Error message', example: 'Invalid credentials')]
        public string $error,

        #[OA\Property(description: 'Error code', example: 401)]
        public int $code,

//        #[OA\Property(description: 'Error details', example: 'Additional error information')]
//        public ?string $details = null,

        #[OA\Property(description: 'Timestamp', example: '2024-01-15T10:30:00Z')]
        public ?string $timestamp
    ) {
        $this->timestamp = (new \DateTimeImmutable())->format('Y-m-d\TH:i:s\Z');
    }
}
