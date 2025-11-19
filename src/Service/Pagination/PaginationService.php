<?php

namespace App\Service\Pagination;

use App\DTO\Transaction\PaginatedResponse;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Serializer\SerializerInterface;

final class PaginationService
{
    public function __construct(
        private readonly SerializerInterface $serializer
    ) {}

    public function createPaginatedResponse(
        Paginator $paginator,
        int $page,
        int $limit,
        string $serializationGroup
    ): PaginatedResponse {

        $totalItems = $paginator->count();
        $totalPages = (int) ceil($totalItems / $limit);

        $entities = iterator_to_array($paginator->getIterator());

        $items = $this->serializer->normalize($entities, 'json', [
            'groups' => [$serializationGroup]
        ]);

        return new PaginatedResponse(
            items: $items,
            page: $page,
            limit: $limit,
            totalItems: $totalItems,
            totalPages: $totalPages
        );
    }
}
