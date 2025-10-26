<?php

namespace App\DTO\Transaction;

use OpenApi\Attributes as OA;

class PaginatedResponse
{
    public function __construct(
        #[OA\Property(description: 'Array of items')]
        public array $content,

        #[OA\Property(description: 'Current page number', example: 0)]
        public int $currentPage,

        #[OA\Property(description: 'Total number of pages', example: 5)]
        public int $totalPages,

        #[OA\Property(description: 'Total number of elements', example: 45)]
        public int $totalElements,

        #[OA\Property(description: 'Page size', example: 10)]
        public int $size
    ) {}
}
