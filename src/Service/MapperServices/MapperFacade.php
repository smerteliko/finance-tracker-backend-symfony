<?php

namespace App\Service\MapperServices;

use App\DTO\Auth\AuthResponse;
use App\DTO\Analytics\AnalyticsResponse;
use App\DTO\Transaction\PaginatedResponse;
use App\Entity\User;
use App\Entity\Transaction;
use App\Entity\Category;
use App\Service\MapperServices\MapperInterface;
use App\Service\MapperServices\Mappers\AnalyticsMapper;
use App\Service\MapperServices\Mappers\PaginatedMapper;
use App\Service\MapperServices\Mappers\UserMapper;

class MapperFacade
{
    /**
     * @param MapperInterface[] $mappers
     */
    public function __construct(
        private iterable $mappers
    ) {}

    /**
     * Map entity to DTO
     *
     * @template T
     * @param object $entity
     * @param class-string<T> $dtoClass
     * @return T
     */
    public function mapToDto(object $entity, string $dtoClass): object
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($entity, $dtoClass)) {
                return $mapper->mapToDto($entity);
            }
        }

        throw new \RuntimeException(sprintf('No mapper found for entity %s to DTO %s', get_class($entity), $dtoClass));
    }

    /**
     * Map array of entities to array of DTOs
     *
     * @template T
     * @param object[] $entities
     * @param class-string<T> $dtoClass
     * @return T[]
     */
    public function mapToDtoArray(array $entities, string $dtoClass): array
    {
        if (empty($entities)) {
            return [];
        }

        $mapper = $this->findMapper($entities[0], $dtoClass);
        return $mapper->mapToDtoArray($entities);
    }

    /**
     * Map user to auth response
     */
    public function mapToAuthResponse(User $user, string $token): AuthResponse
    {
        $userMapper = $this->getMapper(UserMapper::class);
        return $userMapper->mapToAuthResponse($user, $token);
    }

    /**
     * Map analytics data to response
     */
    public function mapToAnalyticsResponse(array $analyticsData): AnalyticsResponse
    {
        $mapper = $this->getMapper(AnalyticsMapper::class);
        return $mapper->mapToDto($analyticsData);
    }

    /**
     * Map paginated data to response
     */
    public function mapToPaginatedResponse(array $paginatedData): PaginatedResponse
    {
        $mapper = $this->getMapper(PaginatedMapper::class);
        return $mapper->mapToDto($paginatedData);
    }

    /**
     * Get specific mapper by class
     */
    public function getMapper(string $mapperClass): MapperInterface
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper instanceof $mapperClass) {
                return $mapper;
            }
        }

        throw new \RuntimeException(sprintf('Mapper %s not found', $mapperClass));
    }

    /**
     * Find mapper for entity and DTO class
     */
    private function findMapper(object $entity, string $dtoClass): MapperInterface
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($entity, $dtoClass)) {
                return $mapper;
            }
        }

        throw new \RuntimeException(sprintf('No mapper found for entity %s to DTO %s', get_class($entity), $dtoClass));
    }
}
