<?php

namespace App\DTO\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(title: 'RegisterRequest', description: 'Data required for user registration.')]
final class RegisterRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[OA\Property(type: 'string', format: 'email', example: 'newuser@example.com')]
    public readonly ?string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    #[OA\Property(type: 'string', format: 'password', example: 'strongpassword123')]
    public readonly ?string $password;

    #[Assert\NotBlank]
    #[OA\Property(type: 'string', example: 'John')]
    public readonly ?string $firstName;

    #[Assert\NotBlank]
    #[OA\Property(type: 'string', example: 'Doe')]
    public readonly ?string $lastName;
}
