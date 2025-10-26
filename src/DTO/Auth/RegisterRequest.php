<?php

namespace App\DTO\Auth;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class RegisterRequest
{
    #[Assert\NotBlank(message: 'common.required_field')]
    #[OA\Property(description: 'User first name', example: 'John')]
    public string $firstName;

    #[Assert\NotBlank(message: 'common.required_field')]
    #[OA\Property(description: 'User last name', example: 'Doe')]
    public string $lastName;

    #[Assert\NotBlank(message: 'common.required_field')]
    #[Assert\Email(message: 'common.invalid_email')]
    #[OA\Property(description: 'User email', example: 'john.doe@example.com')]
    public string $email;

    #[Assert\NotBlank(message: 'common.required_field')]
    #[Assert\Length(min: 6)]
    #[OA\Property(description: 'User password', example: 'password123')]
    public string $password;
}
