<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    private const CATEGORIES = [
        // Income categories
        ['name' => 'Salary', 'type' => 'INCOME', 'color' => '#10b981'],
        ['name' => 'Freelance', 'type' => 'INCOME', 'color' => '#3b82f6'],
        ['name' => 'Investments', 'type' => 'INCOME', 'color' => '#8b5cf6'],
        ['name' => 'Bonus', 'type' => 'INCOME', 'color' => '#06b6d4'],
        ['name' => 'Rental Income', 'type' => 'INCOME', 'color' => '#84cc16'],
        ['name' => 'Dividends', 'type' => 'INCOME', 'color' => '#f59e0b'],

        // Expense categories
        ['name' => 'Food & Dining', 'type' => 'EXPENSE', 'color' => '#ef4444'],
        ['name' => 'Transportation', 'type' => 'EXPENSE', 'color' => '#f97316'],
        ['name' => 'Entertainment', 'type' => 'EXPENSE', 'color' => '#ec4899'],
        ['name' => 'Utilities', 'type' => 'EXPENSE', 'color' => '#6b7280'],
        ['name' => 'Healthcare', 'type' => 'EXPENSE', 'color' => '#84cc16'],
        ['name' => 'Shopping', 'type' => 'EXPENSE', 'color' => '#8b5cf6'],
        ['name' => 'Travel', 'type' => 'EXPENSE', 'color' => '#06b6d4'],
        ['name' => 'Education', 'type' => 'EXPENSE', 'color' => '#d946ef'],
        ['name' => 'Insurance', 'type' => 'EXPENSE', 'color' => '#64748b'],
        ['name' => 'Home Maintenance', 'type' => 'EXPENSE', 'color' => '#f59e0b'],
        ['name' => 'Subscriptions', 'type' => 'EXPENSE', 'color' => '#10b981'],
        ['name' => 'Gifts & Donations', 'type' => 'EXPENSE', 'color' => '#ec4899'],
    ];

    public function load(ObjectManager $manager): void
    {
        $users = [
            $this->getReference('user_john.doe@example.com', User::class),
            $this->getReference('user_jane.smith@example.com', User::class),
            $this->getReference('user_mike.wilson@example.com', User::class),
            $this->getReference('user_sarah.johnson@example.com', User::class),
            $this->getReference('user_alex.brown@example.com', User::class),
        ];

        foreach ($users as $user) {
            foreach (self::CATEGORIES as $categoryData) {
                $category = new Category();
                $category->setName($categoryData['name']);
                $category->setType($categoryData['type']);
                $category->setColor($categoryData['color']);
                $category->setUser($user);

                $manager->persist($category);
                $this->addReference(
                    'category_' . $user->getEmail() . '_' . $categoryData['name'],
                    $category
                );
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
