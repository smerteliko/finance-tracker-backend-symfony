<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Account;
use App\Enum\AccountType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

final class UserFixtures extends Fixture
{
    public const USERS_COUNT = 3;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $availableAccountTypes = AccountType::cases();

        for ($i = 1; $i <= self::USERS_COUNT; $i++) {
            $email = $i === 1 ? 'admin@example.com' : $faker->unique()->safeEmail();
            $firstName = $i === 1 ? 'Admin' : $faker->firstName();
            $lastName = $i === 1 ? 'User' : $faker->lastName();
            $password = $i === 1 ? 'password' : 'password';

            $user = new User();
            $user->setEmail($email);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setSettings(['currency' => 'USD', 'locale' => 'en']); // Setting initial default currency

            $manager->persist($user);
            $this->addReference('user_' . $i, $user);

            $numAccounts = $faker->numberBetween(2, 3);
            shuffle($availableAccountTypes);

            for ($j = 0; $j < $numAccounts; $j++) {
                $accountType = $availableAccountTypes[$j];

                $account = new Account();
                $account->setUser($user);
                $account->setName($accountType->value . ' Account ' . $j);

                $account->setType($accountType );

                $account->setBalance($faker->randomFloat(2, 50, 5000));
                $account->setCurrency('USD');

                $manager->persist($account);
                $this->addReference("user_{$i}_account_{$j}", $account);
            }
        }

        $manager->flush();
    }
}
