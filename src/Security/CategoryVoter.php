<?php

namespace App\Security;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter to determine if a user has access rights (VIEW, EDIT, DELETE) to a Category resource.
 */
class CategoryVoter extends Voter
{
    // Define constants for the supported attributes
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    /**
     * Determines if the Voter supports the attribute and subject.
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Category) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the user's access is granted based on the attribute and subject.
     *
     * @param string $attribute The access level (VIEW, EDIT, DELETE)
     * @param Category $subject The Category entity
     * @param TokenInterface $token The security token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // If the user is not logged in (e.g., anonymous), deny access.
        if (!$user instanceof User) {
            return false;
        }

        /** @var Category $category */
        $category = $subject;

        // In this finance tracker, ownership is the only criterion for all operations.
        return $this->isOwner($category, $user);
    }

    /**
     * Checks if the user is the owner of the category.
     */
    private function isOwner(Category $category, User $user): bool
    {
        return $category->getUser()->getId()->equals($user->getId());
    }
}
