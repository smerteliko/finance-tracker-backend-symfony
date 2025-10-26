<?php

namespace App\DTO\Transaction;

use OpenApi\Attributes as OA;

class TransactionFilterRequest
{
    #[OA\Property(description: 'Page number', example: 0)]
    public ?int $page = 0;

    #[OA\Property(description: 'Page size', example: 10)]
    public ?int $size = 10;

    #[OA\Property(description: 'Start date filter', example: '2024-01-01T00:00:00Z')]
    public ?string $startDate = null;

    #[OA\Property(description: 'End date filter', example: '2024-01-31T23:59:59Z')]
    public ?string $endDate = null;

    #[OA\Property( description: 'Transaction type filter', enum: [ 'INCOME', 'EXPENSE'], example: 'EXPENSE' )]
    public ?string $type = null;

    #[OA\Property(description: 'Category ID filter', example: 1)]
    public ?int $categoryId = null;

    #[OA\Property(description: 'Minimum amount filter', example: 10.0)]
    public ?float $minAmount = null;

    #[OA\Property(description: 'Maximum amount filter', example: 1000.0)]
    public ?float $maxAmount = null;
}
