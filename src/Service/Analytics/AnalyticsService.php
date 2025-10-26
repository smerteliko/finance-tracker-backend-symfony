<?php

namespace App\Service\Analytics;

use App\Entity\User;
use App\Repository\TransactionRepository;

final class AnalyticsService
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository
    ) {}

    public function getAnalytics(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $transactions = $this->transactionRepository->findByUserAndDateRange($user, $startDate, $endDate);

        $totalIncome = 0;
        $totalExpense = 0;
        $expensesByCategory = [];
        $incomeByCategory = [];

        foreach ($transactions as $transaction) {
            $amount = (float) $transaction->getAmount();
            $categoryName = $transaction->getCategory()->getName();
            $categoryColor = $transaction->getCategory()->getColor();

            if ($transaction->getType() === 'INCOME') {
                $totalIncome += $amount;
                $incomeByCategory[$categoryName] = ($incomeByCategory[$categoryName] ?? 0) + $amount;
            } else {
                $totalExpense += $amount;
                $expensesByCategory[$categoryName] = ($expensesByCategory[$categoryName] ?? 0) + $amount;
            }
        }

        return [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
            'expensesByCategory' => $expensesByCategory,
            'incomeByCategory' => $incomeByCategory,
            'transactionCount' => count($transactions),
            'periodStart' => $startDate->format('Y-m-d\TH:i:s\Z'),
            'periodEnd' => $endDate->format('Y-m-d\TH:i:s\Z')
        ];
    }

    public function getSummary(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate): string
    {
        $analytics = $this->getAnalytics($user, $startDate, $endDate);

        $summary = sprintf(
            "Financial Summary (%s - %s)\n\n",
            $startDate->format('M d, Y'),
            $endDate->format('M d, Y')
        );

        $summary .= sprintf("Total Income: $%.2f\n", $analytics['totalIncome']);
        $summary .= sprintf("Total Expenses: $%.2f\n", $analytics['totalExpense']);
        $summary .= sprintf("Net Balance: $%.2f\n", $analytics['balance']);
        $summary .= sprintf("Transactions: %d\n", $analytics['transactionCount']);

        return $summary;
    }
}
