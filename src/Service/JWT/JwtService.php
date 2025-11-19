<?php

namespace App\Service\JWT;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final class JwtService
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager
    ) {}

    public function generateToken(User $user): string
    {
        return $this->jwtManager->create($user);
    }
}
