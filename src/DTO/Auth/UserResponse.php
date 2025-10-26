<?php

namespace App\DTO\Auth;

use OpenApi\Attributes as OA;

class UserResponse
{
    public function __construct(
        #[OA\Property(description: 'User ID', example: 1)]
        public int $id,

        #[OA\Property(description: 'User UUID', example: '123e4567-e89b-12d3-a456-426614174000')]
        public string $uuid,

        #[OA\Property(description: 'User first name', example: 'John')]
        public string $firstName,

        #[OA\Property(description: 'User last name', example: 'Doe')]
        public string $lastName,

        #[OA\Property(description: 'User email', example: 'john.doe@example.com')]
        public string $email,

        #[OA\Property(description: 'Registration date', example: '2024-01-15T10:30:00Z')]
        public string $createdAt
    ) {}
}
