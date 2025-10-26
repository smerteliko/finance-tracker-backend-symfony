<?php

namespace App\Service\Transaction;

use App\Entity\Transaction;
use App\Entity\User;
use App\DTO\Transaction\TransactionRequest;
use App\Repository\TransactionRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;

class TransactionService
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly CategoryRepository    $categoryRepository,
        private readonly UserRepository $userRepository
    ) {}

    /**
     * @throws \Exception
     */
    public function createTransaction(TransactionRequest $request): Transaction
    {
        $user = $this->userRepository->find($request->userId);
        $category = $this->categoryRepository->find($request->categoryId);

        if (!$user || !$category) {
            throw new \Exception('User or category not found.');
        }

        if ($category->getUser()->getId() !== $user->getId()) {
            throw new \Exception('Category does not belong to user.');
        }

        $transaction = new Transaction();
        $transaction->setAmount($request->amount);
        $transaction->setType($request->type);
        $transaction->setDescription($request->description);
        $transaction->setDate(new \DateTimeImmutable($request->date));
        $transaction->setCategory($category);
        $transaction->setUser($user);

        $this->transactionRepository->save($transaction, true);

        return $transaction;
    }

    public function getUserTransactions(User $user, array $filters = []): array
    {
        return $this->transactionRepository->findByUserAndFilters($user, $filters);
    }

    public function getFilteredTransactions(User $user, array $filters): array
    {
        $transactions = $this->transactionRepository->findFilteredTransactions($user, $filters);
        $totalElements = $this->transactionRepository->getTotalCountByUserAndFilters($user, $filters);
        $size = $filters['size'] ?? 10;
        $totalPages = ceil($totalElements / $size);

        return [
            'content' => $transactions,
            'currentPage' => $filters['page'] ?? 0,
            'totalPages' => $totalPages,
            'totalElements' => $totalElements,
            'size' => $size
        ];
    }

    /**
     * @throws \Exception
     */
    public function deleteTransaction(int $id, User $user): void
    {
        $transaction = $this->transactionRepository->findOneBy(['id' => $id, 'user' => $user]);

        if (!$transaction) {
            throw new \Exception('Transaction not found.');
        }

        $this->transactionRepository->remove($transaction, true);
    }
}
