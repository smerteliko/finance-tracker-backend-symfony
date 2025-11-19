<?php

namespace App\DTO\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'AuthResponse', description: 'Response upon successful login or registration.')]
final class AuthResponse
{
    public function __construct(
        #[OA\Property( description: 'JWT Access Token', type: 'string' )]
        public readonly string $token,

        #[OA\Property(schema: '#/components/schemas/UserResponse')]
        public readonly UserResponse $user
    ) {}
}
