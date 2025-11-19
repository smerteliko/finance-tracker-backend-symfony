<?php

namespace App\DTO\Analytics;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'AnalyticsResponse', description: 'Detailed financial analytics for a period.')]
final class AnalyticsResponse
{
    #[OA\Property(type: 'number', format: 'float', example: 1200.50)]
    public readonly float $totalIncome;

    #[OA\Property(type: 'number', format: 'float', example: 800.00)]
    public readonly float $totalExpense;

    #[OA\Property(type: 'number', format: 'float', example: 400.50)]
    public readonly float $balance;

    #[OA\Property(type: 'integer', example: 15)]
    public readonly int $transactionCount;

    #[OA\Property(type: 'string', format: 'date', example: '2025-10-01')]
    public readonly string $periodStart;

    #[OA\Property(type: 'string', format: 'date', example: '2025-10-31')]
    public readonly string $periodEnd;

    // Structure for category breakdown (example: [['categoryName' => 'Food', 'totalAmount' => 350.00]])
    #[OA\Property(type: 'array', items: new OA\Items(type: 'object'))]
    public readonly array $incomeByCategory;

    #[OA\Property(type: 'array', items: new OA\Items(type: 'object'))]
    public readonly array $expensesByCategory;

    public function __construct(
        float $totalIncome,
        float $totalExpense,
        float $balance,
        array $incomeByCategory,
        array $expensesByCategory,
        int $transactionCount,
        \DateTimeInterface $periodStart,
        \DateTimeInterface $periodEnd
    ) {
        $this->totalIncome = $totalIncome;
        $this->totalExpense = $totalExpense;
        $this->balance = $balance;
        $this->incomeByCategory = $incomeByCategory;
        $this->expensesByCategory = $expensesByCategory;
        $this->transactionCount = $transactionCount;
        $this->periodStart = $periodStart->format('Y-m-d');
        $this->periodEnd = $periodEnd->format('Y-m-d');
    }
}
