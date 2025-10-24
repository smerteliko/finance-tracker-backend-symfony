<?php

namespace App\DataFixtures;

use App\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Random\RandomException;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    private const INCOME_DESCRIPTIONS = [
        'Monthly salary payment',
        'Freelance project completion',
        'Stock dividends',
        'Rental property income',
        'Annual bonus',
        'Investment returns',
        'Side business revenue',
        'Consulting fee',
        'Royalty payment',
        'Tax refund'
    ];

    private const EXPENSE_DESCRIPTIONS = [
        'Grocery shopping at Walmart',
        'Monthly electricity bill',
        'Dinner at restaurant',
        'Gas station refill',
        'Netflix subscription',
        'Amazon purchase',
        'Medical checkup',
        'Car insurance payment',
        'Home maintenance supplies',
        'Weekend getaway trip',
        'Online course purchase',
        'Gym membership',
        'Phone bill payment',
        'Coffee at Starbucks',
        'Movie tickets',
        'Book store purchase',
        'Public transport card',
        'Haircut at salon',
        'Birthday gift for friend',
        'Charity donation'
    ];

    /**
     * @throws RandomException
     */
    public function load(ObjectManager $manager): void
    {
        $users = [
            $this->getReference('user_john.doe@example.com'),
            $this->getReference('user_jane.smith@example.com'),
            $this->getReference('user_mike.wilson@example.com'),
            $this->getReference('user_sarah.johnson@example.com'),
            $this->getReference('user_alex.brown@example.com'),
        ];

        foreach ($users as $user) {
            // Create transactions for the last 6 months
            for ($i = 0; $i < 180; $i++) { // ~180 transactions per user
                $transaction = new Transaction();

                // Random date within last 6 months
                $date = new \DateTimeImmutable('-' . rand(0, 180) . ' days');
                $transaction->setDate($date);

                // 30% chance of income, 70% chance of expense
                $isIncome = random_int(1, 100) <= 30;
                $transaction->setType($isIncome ? 'INCOME' : 'EXPENSE');

                if ($isIncome) {
                    $amount = random_int(500, 5000) + (random_int(0, 99) / 100); // $500 - $5000
                    $description = self::INCOME_DESCRIPTIONS[array_rand(self::INCOME_DESCRIPTIONS)];
                    // Get income categories for this user
                    $categories = array_filter(
                        $this->getCategoriesForUser($user->getEmail()),
                        static fn($cat) => $cat->getType() === 'INCOME'
                    );
                } else {
                    $amount = random_int(5, 500) + (random_int(0, 99) / 100); // $5 - $500
                    $description = self::EXPENSE_DESCRIPTIONS[array_rand(self::EXPENSE_DESCRIPTIONS)];
                    // Get expense categories for this user
                    $categories = array_filter(
                        $this->getCategoriesForUser($user->getEmail()),
                        static fn($cat) => $cat->getType() === 'EXPENSE'
                    );
                }

                $transaction->setAmount((string) $amount);
                $transaction->setDescription($description);
                $transaction->setUser($user);
                $transaction->setCategory($categories[array_rand($categories)]);

                $manager->persist($transaction);
            }
        }

        $manager->flush();
    }

    private function getCategoriesForUser(string $userEmail): array
    {
        $categories = [];
        $categoryNames = [
            'Salary', 'Freelance', 'Investments', 'Bonus', 'Rental Income', 'Dividends',
            'Food & Dining', 'Transportation', 'Entertainment', 'Utilities', 'Healthcare',
            'Shopping', 'Travel', 'Education', 'Insurance', 'Home Maintenance',
            'Subscriptions', 'Gifts & Donations'
        ];

        foreach ($categoryNames as $name) {
            $categories[] = $this->getReference('category_' . $userEmail . '_' . $name);
        }

        return $categories;
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
