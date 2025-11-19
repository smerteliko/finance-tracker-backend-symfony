<?php

namespace App\Security;

use App\Entity\Transaction;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TransactionVoter extends Voter
{
    // Define constants for the supported attributes
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        // The subject must be a Transaction entity
        if (!$subject instanceof Transaction) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // 1. If the user is not logged in, deny access.
        if (!$user instanceof User) {
            return false;
        }

        /** @var Transaction $transaction */
        $transaction = $subject;

        // 2. Ownership is the only criterion for all operations (VIEW, EDIT, DELETE).
        return $this->isOwner($transaction, $user);
    }

    private function isOwner(Transaction $transaction, User $user): bool
    {
        // Use equals() for UuidInterface comparison
        return $transaction->getUser()->getId()->equals($user->getId());
    }
}
