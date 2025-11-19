<?php

namespace App\DTO\Transaction;

use App\DTO\Category\CategoryResponse;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;

final class TransactionResponse
{
    public function __construct(
        #[OA\Property(description: 'Transaction ID', example: 1)]
        public string $id,

        #[OA\Property(description: 'Transaction amount', example: 100.50)]
        public float $amount,

        #[OA\Property( description: 'Transaction type', enum: [ 'INCOME', 'EXPENSE'], example: 'EXPENSE' )]
        public string $type,

        #[OA\Property(description: 'Transaction description', example: 'Grocery shopping')]
        public ?string $description,

        #[OA\Property(description: 'Transaction date', example: '2024-01-15T10:30:00Z')]
        public string $date,

        #[OA\Property(description: 'Category information')]
        public CategoryResponse $category,

        public string $accountId,
        public ?string $notes = null,

        #[OA\Property(description: 'Creation date', example: '2024-01-15T10:30:00Z')]
        public string $createdAt,

        #[OA\Property(description: 'Last update date', example: '2024-01-15T10:30:00Z')]
        public string $updatedAt
    ) {}
}
