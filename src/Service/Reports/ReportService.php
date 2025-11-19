<?php

namespace App\Service\Reports;

use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Service\Analytics\AnalyticsService;
use DateTimeImmutable;
use DateTimeInterface;

final class ReportService
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly AnalyticsService $analyticsService
    ) {}

    public function generateCsvReport(User $user, ?string $startDateStr, ?string $endDateStr): string
    {
        try {
            $startDate = $startDateStr ? new DateTimeImmutable($startDateStr) : new DateTimeImmutable('-30 days');
            $endDate = $endDateStr ? new DateTimeImmutable($endDateStr) : new DateTimeImmutable('now');
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid date format provided for analytics period.');
        }

        $transactions = $this->transactionRepository->findTransactionsByDateRange($user, $startDate, $endDate);

        $csvData = [['ID', 'Date', 'Type', 'Amount', 'Category', 'Account', 'Description']];

        foreach ($transactions as $transaction) {
            $csvData[] = [
                $transaction->getId()->toString(),
                $transaction->getDate()->format('Y-m-d H:i:s'),
                $transaction->getType()->value,
                $transaction->getAmount(),
                $transaction->getCategory()->getName(),
                $transaction->getAccount()->getName(),
                $transaction->getDescription(),
            ];
        }

        $output = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($output, $row, ';');
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * @throws \Exception
     */
    public function generateTextSummary(User $user, ?string $startDateStr, ?string $endDateStr): string
    {
        $analytics = $this->analyticsService->getAnalyticsForPeriod($user, $startDateStr, $endDateStr);

        $summary = sprintf("Financial Summary for the period %s to %s:\n",
                           (new DateTimeImmutable($startDateStr))->format('Y-m-d'),
                            (new DateTimeImmutable($endDateStr))->format('Y-m-d')
        );
        $summary .= str_repeat("=", 40) . "\n";
        $summary .= sprintf("Total Income: %.2f\n", $analytics->totalIncome);
        $summary .= sprintf("Total Expense: %.2f\n", $analytics->totalExpense);
        $summary .= sprintf("Net Balance for Period: %.2f\n", $analytics->balance);
        $summary .= sprintf("Total Transactions: %d\n\n", $analytics->transactionCount);

        if (!empty($analytics->expensesByCategory)) {
            $summary .= "Expenses Breakdown:\n";
            foreach ($analytics->expensesByCategory as $item) {
                $summary .= sprintf("  - %s: %.2f\n", $item['categoryName'], $item['totalAmount']);
            }
            $summary .= "\n";
        }

        if (!empty($analytics->incomeByCategory)) {
            $summary .= "Income Breakdown:\n";
            foreach ($analytics->incomeByCategory as $item) {
                $summary .= sprintf("  - %s: %.2f\n", $item['categoryName'], $item['totalAmount']);
            }
        }

        return $summary;
    }
}
