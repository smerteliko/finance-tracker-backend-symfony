<?php

namespace App\DTO\Analytics;

use OpenApi\Attributes as OA;

class AnalyticsResponse
{
    public function __construct(
        #[OA\Property(description: 'Total income', example: 5000.00)]
        public float $totalIncome,

        #[OA\Property(description: 'Total expenses', example: 3200.50)]
        public float $totalExpense,

        #[OA\Property(description: 'Balance', example: 1799.50)]
        public float $balance,

        #[OA\Property(
            description: 'Expenses by category',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                properties: [
                                new OA\Property(property: 'amount', type: 'number'),
                                new OA\Property(property: 'color', type: 'string'),
                                new OA\Property(property: 'count', type: 'integer')
                            ]
            )
        )]
        public array $expensesByCategory,

        #[OA\Property(
            description: 'Income by category',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                properties: [
                                new OA\Property(property: 'amount', type: 'number'),
                                new OA\Property(property: 'color', type: 'string'),
                                new OA\Property(property: 'count', type: 'integer')
                            ]
            )
        )]
        public array $incomeByCategory,

        #[OA\Property(
            description: 'Daily breakdown',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                properties: [
                                new OA\Property(property: 'income', type: 'number'),
                                new OA\Property(property: 'expense', type: 'number')
                            ]
            )
        )]
        public array $dailyBreakdown,

        #[OA\Property(description: 'Total transaction count', example: 45)]
        public int $transactionCount,

        #[OA\Property(description: 'Period start date', example: '2024-01-01T00:00:00Z')]
        public string $periodStart,

        #[OA\Property(description: 'Period end date', example: '2024-01-31T23:59:59Z')]
        public string $periodEnd
    ) {}
}
