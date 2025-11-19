<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use App\Enum\TransactionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $categoriesData = [
            // Expenses
            ['name' => 'Groceries', 'type' => TransactionType::EXPENSE, 'color' => '#FF5733'],
            ['name' => 'Rent/Mortgage', 'type' => TransactionType::EXPENSE, 'color' => '#3357FF'],
            ['name' => 'Transportation', 'type' => TransactionType::EXPENSE, 'color' => '#FFC300'],
            ['name' => 'Utilities', 'type' => TransactionType::EXPENSE, 'color' => '#DAF7A6'],
            ['name' => 'Restaurants', 'type' => TransactionType::EXPENSE, 'color' => '#C70039'],
            ['name' => 'Entertainment', 'type' => TransactionType::EXPENSE, 'color' => '#900C3F'],
            ['name' => 'Shopping', 'type' => TransactionType::EXPENSE, 'color' => '#581845'],
            ['name' => 'Health & Fitness', 'type' => TransactionType::EXPENSE, 'color' => '#117A65'],
            ['name' => 'Travel', 'type' => TransactionType::EXPENSE, 'color' => '#5DADE2'],
            ['name' => 'Education', 'type' => TransactionType::EXPENSE, 'color' => '#AEB6BF'],
            ['name' => 'Investments Loss', 'type' => TransactionType::EXPENSE, 'color' => '#E74C3C'],

            // Incomes
            ['name' => 'Salary', 'type' => TransactionType::INCOME, 'color' => '#33FF57'],
            ['name' => 'Freelance Income', 'type' => TransactionType::INCOME, 'color' => '#2ECC71'],
            ['name' => 'Investments Gain', 'type' => TransactionType::INCOME, 'color' => '#F1C40F'],
            ['name' => 'Gifts', 'type' => TransactionType::INCOME, 'color' => '#D35400'],
        ];

        $userCount = UserFixtures::USERS_COUNT; // Assuming 3 users from UserFixtures

        for ($i = 1; $i <= $userCount; $i++) {
            /** @var \App\Entity\User $user */
            $user = $this->getReference('user_' . $i, User::class);

            foreach ($categoriesData as $key => $data) {
                $category = new Category();
                $category->setUser($user);
                $category->setName($data['name']);

                // CRITICAL FIX: The type is already an Enum object, so we set it directly.
                $category->setType($data['type']);

                $category->setColor($data['color']);

                $manager->persist($category);
                $this->addReference("user_{$i}_category_{$key}", $category);
            }
        }

        for ($k = 0; $k < 10; $k++) {
            $user = $this->getReference('user_' . $faker->numberBetween(1, $userCount), User::class);
            $type = $faker->randomElement([TransactionType::INCOME, TransactionType::EXPENSE]);

            $category = new Category();
            $category->setUser($user);
            $category->setName($faker->word() . ' Custom ' . $k);
            $category->setType($type);
            $category->setColor($faker->hexColor());
            $manager->persist($category);
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
