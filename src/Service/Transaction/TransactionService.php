<?php

namespace App\Service\Transaction;

use App\DTO\Transaction\PaginatedResponse;
use App\DTO\Transaction\TransactionFilterRequest;
use App\DTO\Transaction\TransactionRequest;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionType;
use App\Exception\ResourceNotFoundException;
use App\Repository\TransactionRepository;
use App\Service\Account\AccountService;
use App\Service\Category\CategoryService;
use App\Service\Pagination\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

final class TransactionService
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly AccountService $accountService,
        private readonly CategoryService $categoryService,
        private readonly PaginationService $paginationService,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function getOneTransactionById(string $id): Transaction
    {
        if (!Uuid::isValid($id)) {
            throw new ResourceNotFoundException(sprintf('Transaction ID "%s" is invalid UUID.', $id));
        }

        $transaction = $this->transactionRepository->find(Uuid::fromString($id));

        if (!$transaction) {
            throw new ResourceNotFoundException(sprintf('Transaction with ID %s not found.', $id));
        }

        return $transaction;
    }

    public function getFilteredTransactions(User $user, TransactionFilterRequest $filters): PaginatedResponse
    {
        $paginator = $this->transactionRepository->findPaginatedByUser($user, $filters);

        return $this->paginationService->createPaginatedResponse(
            paginator: $paginator,
            page: $filters->page,
            limit: $filters->limit,
            serializationGroup: 'transaction:read'
        );
    }

    public function createTransaction(User $user, TransactionRequest $request): Transaction
    {
        $category = $this->categoryService->getCategoryById($request->categoryId);

        $account = $this->accountService->getAccountById($request->accountId);
        if (!$account->getUser()->getId()->equals($user->getId())) {
            throw new \InvalidArgumentException('Account does not belong to the current user.');
        }

        $transaction = new Transaction();
        $transaction->setUser($user);
        $transaction->setDescription($request->description);
        $transaction->setAmount($request->amount);
        $transaction->setDate(new \DateTimeImmutable($request->date));
        $transaction->setType(TransactionType::from($request->type));
        $transaction->setCategory($category);
        $transaction->setAccount($account);
        $transaction->setNotes($request->notes);

        $this->accountService->updateBalance(
            account: $account,
            amount: $request->amount,
            type: TransactionType::from($request->type)
        );

        $this->transactionRepository->save($transaction, TRUE);

        return $transaction;
    }

    public function updateTransaction(Transaction $transaction, TransactionRequest $request): Transaction
    {
        $oldAmount = $transaction->getAmount();
        $oldType = $transaction->getType();
        $oldAccount = $transaction->getAccount();

        $newCategory = $this->categoryService->getCategoryById($request->categoryId);
        $newAccount = $this->accountService->getAccountById($request->accountId);

        if (!$newAccount->getUser()->getId()->equals($transaction->getUser()->getId())) {
            throw new \InvalidArgumentException('New account does not belong to the current user.');
        }

        $transaction->setDescription($request->description);
        $transaction->setAmount($request->amount);
        $transaction->setDate(new \DateTimeImmutable($request->date));
        $transaction->setType(TransactionType::from($request->type));
        $transaction->setCategory($newCategory);
        $transaction->setAccount($newAccount);
        $transaction->setNotes($request->notes);

        // Update Account Balances
        if (!$oldAccount->getId()->equals($newAccount->getId())) {
            // Rollback from OLD Account (treat old transaction as deleted)
            $this->accountService->updateBalance($oldAccount, 0.0, TransactionType::INCOME, $oldAmount, $oldType);

            // Apply new amount to NEW Account (treat new transaction as created)
            $this->accountService->updateBalance($newAccount, $request->amount, TransactionType::from($request->type));
        } else {
            // Same Account: simple adjustment (rollback old, apply new)
            $this->accountService->updateBalance(
                account: $newAccount,
                amount: $request->amount,
                type: TransactionType::from($request->type),
                oldAmount: $oldAmount,
                oldType: $oldType
            );
        }

        $this->entityManager->flush();

        return $transaction;
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        $amount = $transaction->getAmount();
        $type = $transaction->getType();
        $account = $transaction->getAccount();

        // Rollback Account Balance (New amount is 0)
        $this->accountService->updateBalance(
            account: $account,
            amount: 0.0,
            type: TransactionType::INCOME, // New type is irrelevant
            oldAmount: $amount,
            oldType: $type
        );

        $this->transactionRepository->remove($transaction, TRUE);
    }
}
