<?php

namespace App\DTO\Category;

use App\Enum\TransactionType;
use OpenApi\Attributes as OA;

#[OA\Schema(title: 'CategoryResponse', description: 'Category data returned by the API.')]
final class CategoryResponse
{
    public function __construct(
        #[OA\Property( description: 'Unique Category identifier', type: 'string', format: 'uuid', example: '123e4567-e89b-12d3-a456-426614174000' )]
        public readonly string $id,

        #[OA\Property( description: 'Category name', type: 'string', example: 'Groceries' )]
        public readonly string $name,

        #[OA\Property( description: 'Transaction type', type: 'string', enum: [ 'INCOME', 'EXPENSE'] )]
        public readonly TransactionType $type,

        #[OA\Property( description: 'Hex color code', type: 'string', example: '#FF5733', nullable: true )]
        public readonly ?string $color,

        #[OA\Property( description: 'Creation timestamp', type: 'string', format: 'date-time' )]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property( description: 'Last update timestamp', type: 'string', format: 'date-time', nullable: true )]
        public readonly ?\DateTimeImmutable $updatedAt = null,
    ) {}
}
