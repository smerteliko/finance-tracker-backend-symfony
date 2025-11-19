<?php

namespace App\DTO\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

final class LoginRequest
{
    #[Assert\NotBlank(message: 'common.required_field')]
    #[Assert\Email(message: 'common.invalid_email')]
    #[OA\Property(description: 'User email', example: 'admin@example.com')]
    public string $email;

    #[Assert\NotBlank(message: 'common.required_field')]
    #[OA\Property(description: 'User password', example: 'password')]
    public string $password;
}
