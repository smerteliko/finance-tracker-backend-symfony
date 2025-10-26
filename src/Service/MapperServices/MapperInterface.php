<?php

namespace App\Service\MapperServices;

interface MapperInterface
{
    /**
     * Check if the mapper supports the given entity and DTO
     */
    public function supports(object $entity, string $dtoClass): bool;

    /**
     * Map entity to DTO
     */
    public function mapToDto(object $entity): object;

    /**
     * Map DTO to entity
     */
    public function mapToEntity(object $dto): object;

    /**
     * Map array of entities to array of DTOs
     *
     * @param object[] $entities
     * @return object[]
     */
    public function mapToDtoArray(array $entities): array;
}
