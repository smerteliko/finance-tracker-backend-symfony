<?php

namespace App\Service\MapperServices\Mappers;

use App\DTO\Auth\AuthResponse;
use App\DTO\Auth\UserResponse;
use App\Entity\User;
use App\Service\MapperServices\AbstractMapper;

class UserMapper extends AbstractMapper
{
    public function __construct()
    {
        $this->entityToDtoMap = [
            User::class => UserResponse::class,
        ];
    }

    public function mapToDto(object $entity): object
    {
        if (!$entity instanceof User) {
            throw new \InvalidArgumentException('Entity must be an instance of User');
        }

        return new UserResponse(
            id: $entity->getId(),
            uuid: $entity->getUuid()->toString(),
            firstName: $entity->getFirstName(),
            lastName: $entity->getLastName(),
            email: $entity->getEmail(),
            createdAt: $entity->getCreatedAt()?->format('Y-m-d\TH:i:s\Z')
        );
    }

    public function mapToEntity(object $dto): object
    {
        throw new \RuntimeException('Mapping from DTO to User entity is not implemented');
    }

    public function mapToAuthResponse(User $user, string $token): AuthResponse
    {
        return new AuthResponse(
            token: $token,
            userId: $user->getId(),
            firstName: $user->getFirstName(),
            lastName: $user->getLastName(),
            email: $user->getEmail(),
        );
    }

    protected function getEntityClass(): string
    {
        return User::class;
    }

    protected function getDtoClass(): string
    {
        return UserResponse::class;
    }
}
