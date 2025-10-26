<?php

namespace App\Service\MapperServices\Mappers;

use App\DTO\Category\CategoryResponse;
use App\Entity\Category;
use App\Service\MapperServices\AbstractMapper;

class CategoryMapper extends AbstractMapper
{
    public function __construct()
    {
        $this->entityToDtoMap = [
            Category::class => CategoryResponse::class,
        ];
    }

    public function mapToDto(object $entity): object
    {
        if (!$entity instanceof Category) {
            throw new \InvalidArgumentException('Entity must be an instance of Category');
        }

        return new CategoryResponse(
            id: $entity->getId(),
            uuid: $entity->getUuid()->toString(),
            name: $entity->getName(),
            color: $entity->getColor(),
            type: $entity->getType(),
            createdAt: $entity->getCreatedAt()?->format('Y-m-d\TH:i:s\Z')
        );
    }

    public function mapToEntity(object $dto): object
    {
        throw new \RuntimeException('Mapping from DTO to Category entity is not implemented');
    }

    protected function getEntityClass(): string
    {
        return Category::class;
    }

    protected function getDtoClass(): string
    {
        return CategoryResponse::class;
    }
}
