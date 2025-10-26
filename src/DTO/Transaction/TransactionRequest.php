<?php

namespace App\DTO\Transaction;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class TransactionRequest
{
    #[Assert\NotBlank(message: 'common.required_field')]
    #[Assert\Type('numeric')]
    #[Assert\Positive(message: 'validation.amount_positive')]
    #[OA\Property(description: 'Transaction amount', example: 100.50)]
    public string $amount;

    #[Assert\NotBlank(message: 'common.required_field')]
    #[Assert\Choice(['INCOME', 'EXPENSE'], message: 'validation.type_invalid')]
    #[OA\Property( description: 'Transaction type', enum: [ 'INCOME', 'EXPENSE'], example: 'EXPENSE' )]
    public string $type;

    #[OA\Property(description: 'Transaction description', example: 'Grocery shopping')]
    public ?string $description = null;

    #[Assert\NotBlank(message: 'common.required_field')]
    #[Assert\DateTime(message: 'validation.date_invalid')]
    #[OA\Property(description: 'Transaction date', example: '2024-01-15T10:30:00Z')]
    public string $date;

    #[Assert\NotBlank(message: 'common.required_field')]
    #[Assert\Type('integer')]
    #[OA\Property(description: 'Category ID', example: 1)]
    public int $categoryId;

    #[Assert\NotBlank(message: 'common.required_field')]
    #[Assert\Type('integer')]
    #[OA\Property(description: 'User ID', example: 1)]
    public int $userId;
}
