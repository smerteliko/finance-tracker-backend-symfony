<?php

namespace App\Service\Analytics;

use App\DTO\Analytics\AnalyticsResponse;
use App\Entity\User;
use App\Enum\TransactionType;
use App\Repository\TransactionRepository;
use DateTimeImmutable;
use DateTimeInterface;

final class AnalyticsService
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository
    ) {}

    /**
     * Calculates the user's current overall balance (Income - Expense for all time).
     *
     * @param User $user
     * @return float
     */
    public function getCurrentBalance(User $user): float
    {
        $income = $this->transactionRepository->sumByType($user, TransactionType::INCOME);
        $expense = $this->transactionRepository->sumByType($user, TransactionType::EXPENSE);

        return $income - $expense;
    }

    /**
     * Calculates detailed analytics for a given period.
     * Note: This method should be updated to return a raw array and let the controller/serializer handle mapping to AnalyticsResponse.
     * However, to maintain the DTO structure for non-entity responses, we keep the DTO creation.
     *
     * @param User $user
     * @param ?string $startDateStr
     * @param ?string $endDateStr
     * @return AnalyticsResponse
     */
    public function getAnalyticsForPeriod(User $user, ?string $startDateStr, ?string $endDateStr): AnalyticsResponse
    {
        try {
            $startDate = $startDateStr ? new DateTimeImmutable($startDateStr) : new DateTimeImmutable('-30 days');
            $endDate = $endDateStr ? new DateTimeImmutable($endDateStr) : new DateTimeImmutable('now');
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid date format provided for analytics period.');
        }

        $incomeTotal = $this->transactionRepository->sumByTypeAndPeriod($user, $startDate, $endDate, TransactionType::INCOME);
        $expenseTotal = $this->transactionRepository->sumByTypeAndPeriod($user, $startDate, $endDate, TransactionType::EXPENSE);
        $transactionCount = $this->transactionRepository->countByPeriod($user, $startDate, $endDate);

        $incomeByCategory = $this->transactionRepository->sumByTransactionTypeAndCategory($user, TransactionType::INCOME, $startDate, $endDate);
        $expensesByCategory = $this->transactionRepository->sumByTransactionTypeAndCategory($user, TransactionType::EXPENSE, $startDate, $endDate);

        return new AnalyticsResponse(
            totalIncome: (float) $incomeTotal,
            totalExpense: (float) $expenseTotal,
            balance: (float) $incomeTotal - $expenseTotal,
            incomeByCategory: $incomeByCategory, // Needs separate DTO mapping if necessary
            expensesByCategory: $expensesByCategory,
            transactionCount: $transactionCount,
            periodStart: $startDate,
            periodEnd: $endDate
        );
    }
}
