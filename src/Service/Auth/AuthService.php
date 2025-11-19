<?php

namespace App\Service\Auth;

use App\DTO\Auth\AuthResponse;
use App\DTO\Auth\LoginRequest;
use App\DTO\Auth\RegisterRequest;
use App\DTO\Auth\UserResponse;
use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Repository\UserRepository;
use App\Service\JWT\JwtService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Serializer\SerializerInterface;

final class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JwtService $jwtService,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * Registers a new user.
     *
     * @param RegisterRequest $request
     * @return User
     * @throws UserAlreadyExistsException
     */
    public function register(RegisterRequest $request): User
    {
        if ($this->userRepository->findOneBy(['email' => $request->email])) {
            throw new UserAlreadyExistsException();
        }

        $user = new User();
        $user->setEmail($request->email);
        $user->setFirstName($request->firstName);
        $user->setLastName($request->lastName);
        $user->setPassword($this->passwordHasher->hashPassword($user, $request->password));

        $this->userRepository->save($user, true); // Assuming UserRepository has save method

        return $user;
    }

    /**
     * Verifies user credentials during login.
     *
     * @param LoginRequest $request
     * @return User
     * @throws BadCredentialsException
     */
    public function verifyCredentials(LoginRequest $request): User
    {
        $user = $this->userRepository->findOneBy(['email' => $request->email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $request->password)) {
            throw new BadCredentialsException('Invalid email or password.');
        }

        return $user;
    }

    /**
     * Creates an AuthResponse DTO from User Entity and JWT Token using the Serializer.
     *
     * @param User $user
     * @param string $token
     * @return AuthResponse
     */
    public function createAuthResponse(User $user, string $token): AuthResponse
    {
        $userDtoArray = $this->serializer->normalize($user, 'json', [
            'groups' => ['user:read']
        ]);

        $userResponse = $this->serializer->denormalize($userDtoArray, UserResponse::class, 'json');

        return new AuthResponse(
            token: $token,
            user: $userResponse
        );
    }
}
