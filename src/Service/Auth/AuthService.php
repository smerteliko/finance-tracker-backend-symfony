<?php

namespace App\Service\Auth;

use App\DTO\Auth\LoginRequest;
use App\DTO\Auth\RegisterRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Service\JWT\JwtService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private CategoryRepository $categoryRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JwtService $jwtService
    ) {}

    /**
     * @throws \Exception
     */
    public function register(RegisterRequest $request): array
    {
        if ($this->userRepository->findByEmail($request->email)) {
            throw new \Exception('User with this email already exists.');
        }

        $user = new User();
        $user->setFirstName($request->firstName);
        $user->setLastName($request->lastName);
        $user->setEmail($request->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $request->password));

        // Save user
        $this->userRepository->save($user, true);

        // Create default categories for the user
        $defaultCategories = $this->categoryRepository->findDefaultCategoriesForUser($user);
        foreach ($defaultCategories as $category) {
            $this->categoryRepository->save($category, false);
        }
        $this->userRepository->getEntityManager()->flush();

        $token = $this->jwtService->generateToken($user);

        return [
            'token' => $token,
            'userId' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail()
        ];
    }

    public function login(LoginRequest $request): array
    {
        $user = $this->userRepository->findByEmail($request->email);
        if (!$this->passwordHasher->isPasswordValid($user, $request->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }
        $token = $this->jwtService->generateToken($user);
        return [
            'token' => $token,
        ];
    }

    public function getUserByEmail(string $email): User {
       return $this->userRepository->findByEmail($email);

    }
}
