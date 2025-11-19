<?php

namespace App\DTO\Transaction;

use App\Enum\TransactionType;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(title: 'TransactionRequest', description: 'Data required to create or update a transaction.')]
final class TransactionRequest
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[OA\Property( description: 'UUID of the associated account.', type: 'string', format: 'uuid' )]
    public readonly ?string $accountId;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[OA\Property( description: 'UUID of the associated category.', type: 'string', format: 'uuid' )]
    public readonly ?string $categoryId;

    #[Assert\NotBlank]
    #[Assert\Type('float')]
    #[Assert\Positive]
    #[OA\Property(type: 'number', format: 'float', example: 45.99)]
    public readonly float $amount;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [TransactionType::class, 'values'])]
    #[OA\Property(type: 'string', enum: ['INCOME', 'EXPENSE'])]
    public readonly ?string $type;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    #[OA\Property(type: 'string', format: 'date-time', example: '2025-11-17T12:00:00Z')]
    public readonly ?string $date;

    #[Assert\Length(max: 255)]
    #[OA\Property( type: 'string', example: 'Grocery shopping', nullable: true )]
    public readonly ?string $description;

    #[Assert\Length(max: 1024)]
    #[OA\Property( description: 'Detailed notes.', type: 'string', nullable: true )]
    public readonly ?string $notes;
}
