<?php

namespace App\DTO\Account;

use App\Enum\AccountType;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(title: 'AccountRequest', description: 'Data required to create or update a financial account.')]
final class AccountRequest
{
    #[Assert\NotBlank]
    #[OA\Property(type: 'string', example: 'Main Checking')]
    public readonly ?string $name;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [AccountType::class, 'values'])]
    #[OA\Property(type: 'string', enum: ['CHECKING', 'SAVINGS', 'CASH', 'CREDIT_CARD', 'INVESTMENT'])]
    public readonly ?string $type;

    #[Assert\NotBlank]
    #[Assert\Type('float')]
    #[Assert\PositiveOrZero]
    #[OA\Property( description: 'Initial balance (only used on POST).', type: 'number', format: 'float', example: 500.00 )]
    public readonly float $initialBalance;

    #[OA\Property( type: 'string', example: 'EUR', nullable: true )]
    public readonly ?string $currency;
}
