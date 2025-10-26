<?php

namespace App\Service\JWT;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtService
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager
    ) {}

    public function generateToken(UserInterface $user): string
    {
        return $this->jwtManager->create($user);
    }

    public function getTokenPayload(string $token): array
    {
        $parts = explode('.', $token);
        $payload = json_decode(base64_decode($parts[1]), true);

        return $payload ?? [];
    }
}
