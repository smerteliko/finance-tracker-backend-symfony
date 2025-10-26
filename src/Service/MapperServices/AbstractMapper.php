<?php

namespace App\Service\MapperServices;

abstract class AbstractMapper implements MapperInterface
{
    /**
     * @var array<string, string> Mapping of entity classes to DTO classes
     */
    protected array $entityToDtoMap = [];

    /**
     * @var array<string, string> Mapping of DTO classes to entity classes
     */
    protected array $dtoToEntityMap = [];

    public function supports(object $entity, string $dtoClass): bool
    {
        $entityClass = get_class($entity);

        // Check if we support mapping from this entity to the requested DTO
        if (isset($this->entityToDtoMap[$entityClass]) && $this->entityToDtoMap[$entityClass] === $dtoClass) {
            return true;
        }

        // Check if we support mapping from this DTO to the entity
        if (isset($this->dtoToEntityMap[$dtoClass]) && $this->dtoToEntityMap[$dtoClass] === $entityClass) {
            return true;
        }

        return false;
    }

    public function mapToDtoArray(array $entities): array
    {
        return array_map([$this, 'mapToDto'], $entities);
    }

    /**
     * Get supported entity class
     */
    abstract protected function getEntityClass(): string;

    /**
     * Get supported DTO class
     */
    abstract protected function getDtoClass(): string;
}
