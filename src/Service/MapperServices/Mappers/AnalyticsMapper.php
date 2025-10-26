<?php

namespace App\Service\MapperServices\Mappers;

use App\DTO\Analytics\AnalyticsResponse;
use App\Service\MapperServices\AbstractMapper;

class AnalyticsMapper extends AbstractMapper
{
    public function __construct()
    {
        // Analytics doesn't map from a specific entity
        $this->entityToDtoMap = [];
    }

    public function supports(object $entity, string $dtoClass): bool
    {
        return $dtoClass === AnalyticsResponse::class && is_array($entity);
    }

    public function mapToDto(object $entity): object
    {
        if (!is_array($entity)) {
            throw new \InvalidArgumentException('Entity must be an array for AnalyticsMapper');
        }

        return new AnalyticsResponse(
            totalIncome: $entity['totalIncome'],
            totalExpense: $entity['totalExpense'],
            balance: $entity['balance'],
            expensesByCategory: $entity['expensesByCategory'],
            incomeByCategory: $entity['incomeByCategory'],
            dailyBreakdown: $entity['dailyBreakdown'],
            transactionCount: $entity['transactionCount'],
            periodStart: $entity['periodStart'],
            periodEnd: $entity['periodEnd']
        );
    }

    public function mapToEntity(object $dto): object
    {
        throw new \RuntimeException('Mapping from DTO to analytics array is not implemented');
    }

    protected function getEntityClass(): string
    {
        return 'array';
    }

    protected function getDtoClass(): string
    {
        return AnalyticsResponse::class;
    }
}
