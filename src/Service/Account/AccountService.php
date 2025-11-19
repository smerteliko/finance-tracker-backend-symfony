<?php

namespace App\Service\Account;

use App\DTO\Account\AccountRequest;
use App\Entity\Account;
use App\Entity\User;
use App\Enum\AccountType;
use App\Enum\TransactionType;
use App\Exception\ResourceNotFoundException;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

final class AccountService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccountRepository $accountRepository
    ) {}

    public function getAccountById(string $id): Account
    {
        if (!Uuid::isValid($id)) {
            throw new ResourceNotFoundException(sprintf('Account ID "%s" is invalid UUID.', $id));
        }

        $account = $this->accountRepository->find(Uuid::fromString($id));

        if (!$account) {
            throw new ResourceNotFoundException(sprintf('Account with ID %s not found.', $id));
        }

        return $account;
    }

    public function getAllAccounts(User $user): array
    {
        return $this->accountRepository->findBy(['user' => $user]);
    }

    public function createAccount(User $user, AccountRequest $request): Account
    {
        // Check for duplicate account name
        $existingAccount = $this->accountRepository->findOneBy(['user' => $user, 'name' => $request->name]);
        if ($existingAccount) {
            throw new \InvalidArgumentException(sprintf('An account named "%s" already exists.', $request->name));
        }

        $account = new Account();
        $account->setUser($user);
        $account->setName($request->name);
        $account->setType(AccountType::from($request->type));
        $account->setBalance($request->initialBalance);
        $account->setCurrency($request->currency ?? $user->getSettings()['currency'] ?? 'USD');

        $this->accountRepository->save($account, true);

        return $account;
    }
    public function updateAccount(Account $account, AccountRequest $request): Account
    {
        $account->setName($request->name);
        $account->setType(AccountType::from($request->type));
        $account->setCurrency($request->currency ?? $account->getCurrency());
        // Note: InitialBalance is ignored here; balance is only modified via transactions.

        $this->entityManager->flush();

        return $account;
    }

    public function deleteAccount(Account $account): void
    {
        if ($account->getTransactions()->count() > 0) {
            throw new \InvalidArgumentException('Account cannot be deleted because it has associated transactions.');
        }
        $this->accountRepository->remove($account, true);

    }

    /**
     * CRITICAL: Updates the account balance based on a transaction change.
     * This method manages the balance rollback and application.
     *
     * @param Account $account
     * @param float $amount The amount of the NEW transaction.
     * @param TransactionType $type The type of the NEW transaction.
     * @param float $oldAmount The previous amount (used for rollback). Defaults to 0.
     * @param TransactionType $oldType The previous type (used for rollback). Defaults to INCOME.
     */
    public function updateBalance(
        Account $account,
        float $amount,
        TransactionType $type,
        float $oldAmount = 0.0,
        TransactionType $oldType = TransactionType::INCOME): void
    {
        $currentBalance = $account->getBalance();

        // 1. Rollback old amount if updating/deleting
        if ($oldAmount > 0.0) {
            // Reverse the effect of the OLD transaction
            $currentBalance = ($oldType === TransactionType::INCOME)
                ? $currentBalance - $oldAmount
                : $currentBalance + $oldAmount;
        }

        // 2. Apply new amount
        $newBalance = ($type === TransactionType::INCOME)
            ? $currentBalance + $amount
            : $currentBalance - $amount;

        $account->setBalance($newBalance);

        // No flush here; handled by TransactionService.
    }
}
