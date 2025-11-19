<?php

namespace App\Security;

use App\Entity\Account;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter to determine if a user has access rights (VIEW, EDIT, DELETE) to an Account resource.
 */
class AccountVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Account) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Deny access if not a logged-in User
        if (!$user instanceof User) {
            return false;
        }

        /** @var Account $account */
        $account = $subject;

        return $this->isOwner($account, $user);
    }

    /**
     * Checks if the user is the owner of the account.
     */
    private function isOwner(Account $account, User $user): bool
    {
        return $account->getUser()->getId()->equals($user->getId());
    }
}
