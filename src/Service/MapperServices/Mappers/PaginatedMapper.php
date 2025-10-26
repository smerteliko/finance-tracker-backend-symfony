<?php

namespace App\Service\MapperServices\Mappers;

use App\DTO\Transaction\PaginatedResponse;
use App\Service\MapperServices\AbstractMapper;

class PaginatedMapper extends AbstractMapper
{
    public function __construct()
    {
        // Paginated doesn't map from a specific entity
        $this->entityToDtoMap = [];
    }

    public function supports(object $entity, string $dtoClass): bool
    {
        return $dtoClass === PaginatedResponse::class && is_array($entity);
    }

    public function mapToDto(object $entity): object
    {
        if (!is_array($entity)) {
            throw new \InvalidArgumentException('Entity must be an array for PaginatedMapper');
        }

        return new PaginatedResponse(
            content: $entity['content'],
            currentPage: $entity['currentPage'],
            totalPages: $entity['totalPages'],
            totalElements: $entity['totalElements'],
            size: $entity['size']
        );
    }

    public function mapToEntity(object $dto): object
    {
        throw new \RuntimeException('Mapping from DTO to paginated array is not implemented');
    }

    protected function getEntityClass(): string
    {
        return 'array';
    }

    protected function getDtoClass(): string
    {
        return PaginatedResponse::class;
    }
}
