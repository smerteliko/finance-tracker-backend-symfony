<?php

namespace App\DTO\Category;

use OpenApi\Attributes as OA;

class CategoryResponse
{
    public function __construct(
        #[OA\Property(description: 'Category ID', example: 1)]
        public int $id,

        #[OA\Property(description: 'Category UUID', example: '123e4567-e89b-12d3-a456-426614174000')]
        public string $uuid,

        #[OA\Property(description: 'Category name', example: 'Food & Dining')]
        public string $name,

        #[OA\Property(description: 'Category color', example: '#FF6384')]
        public string $color,

        #[OA\Property( description: 'Category type', enum: [ 'INCOME', 'EXPENSE'], example: 'EXPENSE' )]
        public string $type,

        #[OA\Property(description: 'Creation date', example: '2024-01-15T10:30:00Z')]
        public string $createdAt
    ) {}
}
