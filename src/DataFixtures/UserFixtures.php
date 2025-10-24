<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USERS = [
        [
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'currency' => 'USD'
        ],
        [
            'email' => 'jane.smith@example.com',
            'password' => 'password123',
            'firstName' => 'Jane',
            'lastName' => 'Smith',
            'currency' => 'EUR'
        ],
        [
            'email' => 'mike.wilson@example.com',
            'password' => 'password123',
            'firstName' => 'Mike',
            'lastName' => 'Wilson',
            'currency' => 'GBP'
        ],
        [
            'email' => 'sarah.johnson@example.com',
            'password' => 'password123',
            'firstName' => 'Sarah',
            'lastName' => 'Johnson',
            'currency' => 'USD'
        ],
        [
            'email' => 'alex.brown@example.com',
            'password' => 'password123',
            'firstName' => 'Alex',
            'lastName' => 'Brown',
            'currency' => 'CAD'
        ]
    ];

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);

            $manager->persist($user);
            $this->addReference('user_' . $userData['email'], $user);
        }

        $manager->flush();
    }
}
