<?php

namespace App\DataFixtures;

use App\Entity\Transaction;
use App\Entity\Account;
use App\Entity\Category;
use App\Entity\User;
use App\Enum\TransactionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    private const TOTAL_TRANSACTIONS = 1000;
    private const BATCH_SIZE = 50;
    private const CATEGORIES_PER_USER = 13; // Based on CategoryFixtures

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $allCategories = [];
        $userCount = UserFixtures::USERS_COUNT;

        for ($i = 1; $i <= $userCount; $i++) {
            $userCategories = [];
            for ($k = 0; $k < self::CATEGORIES_PER_USER; $k++) {
                if ($this->hasReference("user_{$i}_category_{$k}",Category::class)) {
                    // CRITICAL FIX: Use the required getReference signature
                    $userCategories[] = $this->getReference("user_{$i}_category_{$k}", Category::class);
                }
            }
            if (!empty($userCategories)) {
                $allCategories[$i] = $userCategories;
            }
        }

        for ($t = 1; $t <= self::TOTAL_TRANSACTIONS; $t++) {
            $userId = $faker->numberBetween(1, $userCount);
            $accountIndex = $faker->numberBetween(0, 2);
            $accountRef = "user_{$userId}_account_{$accountIndex}";

            if (!$this->hasReference('user_' . $userId, User::class) || !$this->hasReference($accountRef, Account::class)) {
                continue;
            }

            /** @var User $user */
            $user = $this->getReference('user_' . $userId, User::class);
            /** @var Account $account */
            $account = $this->getReference($accountRef, Account::class);

            $userCategories = $allCategories[$userId] ?? null;
            if (empty($userCategories)) { continue; }

            /** @var User $userDetached */
            $userDetached = $this->getReference('user_' . $userId, User::class);
            /** @var Account $accountDetached */
            $accountDetached = $this->getReference($accountRef, Account::class);

            $userCategories = $allCategories[$userId] ?? null;
            if (empty($userCategories)) { continue; }

            /** @var Category $categoryDetached */
            $categoryDetached = $faker->randomElement($userCategories);

            $user = $manager->find(User::class, $userDetached->getId());
            /** @var Account $account */
            $account = $manager->find(Account::class, $accountDetached->getId());
            /** @var Category $category */
            $category = $manager->find(Category::class, $categoryDetached->getId());

            $transactionType = $category->getType();
            $amount = $faker->randomFloat(2, 5, $transactionType === TransactionType::INCOME ? 1500 : 500);
            $transactionDate = $faker->dateTimeBetween('-1 year', 'now');

            $transaction = new Transaction();
            $transaction->setUser($user);
            $transaction->setAccount($account);
            $transaction->setCategory($category);
            $transaction->setAmount($amount);
            $transaction->setType($transactionType);
            $transaction->setDate(\DateTimeImmutable::createFromMutable($transactionDate));
            $transaction->setDescription($faker->sentence(3));
            $transaction->setNotes($faker->boolean(20) ? $faker->text(50) : null);

            $manager->persist($transaction);

            $currentBalance = $account->getBalance();

            if ($transactionType === TransactionType::INCOME) {
                $account->setBalance($currentBalance + $amount);
            } else {
                $account->setBalance($currentBalance - $amount);
            }

            if ($t % self::BATCH_SIZE === 0) {
                $manager->flush();
                $manager->clear();

            }
        }

        $manager->flush(); // Final flush for remaining entities
        $manager->clear();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
