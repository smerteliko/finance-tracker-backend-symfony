<?php

namespace App\DTO\Auth;

use OpenApi\Attributes as OA;

class AuthResponse
{
    public function __construct(
        #[OA\Property(description: 'JWT token', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...')]
        public string $token,

        #[OA\Property(description: 'User ID', example: 1)]
        public int $userId,

        #[OA\Property(description: 'User first name', example: 'John')]
        public string $firstName,

        #[OA\Property(description: 'User last name', example: 'Doe')]
        public string $lastName,

        #[OA\Property(description: 'User email', example: 'john.doe@example.com')]
        public string $email,

    ) {}
}
