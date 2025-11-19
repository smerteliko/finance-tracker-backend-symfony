<?php

namespace App\DTO\Transaction;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'PaginatedResponse', description: 'Standard paginated API response.')]
final class PaginatedResponse
{
    #[OA\Property( description: 'List of serialized entities (e.g., Transaction or Category).', type: 'array' )]
    public readonly array $items;

    #[OA\Property(type: 'integer', example: 1)]
    public readonly int $page;

    #[OA\Property(type: 'integer', example: 10)]
    public readonly int $limit;

    #[OA\Property(type: 'integer', example: 45)]
    public readonly int $totalItems;

    #[OA\Property(type: 'integer', example: 5)]
    public readonly int $totalPages;

    public function __construct(
        array $items,
        int $page,
        int $limit,
        int $totalItems,
        int $totalPages
    ) {
        $this->items = $items;
        $this->page = $page;
        $this->limit = $limit;
        $this->totalItems = $totalItems;
        $this->totalPages = $totalPages;
    }
}
