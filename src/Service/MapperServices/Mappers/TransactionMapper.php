<?php

namespace App\Service\MapperServices\Mappers;

use App\DTO\Transaction\TransactionResponse;
use App\Entity\Transaction;
use App\Service\MapperServices\AbstractMapper;

class TransactionMapper extends AbstractMapper
{
    public function __construct(
        public CategoryMapper $categoryMapper
    ) {
        $this->entityToDtoMap = [
            Transaction::class => TransactionResponse::class,
        ];
    }

    public function mapToDto(object $entity): object
    {
        if (!$entity instanceof Transaction) {
            throw new \InvalidArgumentException('Entity must be an instance of Transaction');
        }

        $categoryResponse = $this->categoryMapper->mapToDto($entity->getCategory());

        return new TransactionResponse(
            id: $entity->getId(),
            uuid: $entity->getUuid()->toString(),
            amount: (float) $entity->getAmount(),
            type: $entity->getType(),
            description: $entity->getDescription(),
            date: $entity->getDate()?->format('Y-m-d\TH:i:s\Z'),
            category: $categoryResponse,
            createdAt: $entity->getCreatedAt()?->format('Y-m-d\TH:i:s\Z'),
            updatedAt: $entity->getUpdatedAt()?->format('Y-m-d\TH:i:s\Z')
        );
    }

    public function mapToEntity(object $dto): object
    {
        throw new \RuntimeException('Mapping from DTO to Transaction entity is not implemented');
    }

    protected function getEntityClass(): string
    {
        return Transaction::class;
    }

    protected function getDtoClass(): string
    {
        return TransactionResponse::class;
    }
}
