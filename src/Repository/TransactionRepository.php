<?php

namespace App\Repository;

use App\DTO\Transaction\TransactionFilterRequest;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionType;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Transaction::class);
    }

    public function save(Transaction $transaction, bool $flush = FALSE): void {
        $this->getEntityManager()->persist($transaction);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $transaction,
                           bool        $flush = FALSE): void {
        $this->getEntityManager()->remove($transaction);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findTransactionsByDateRange(User $user, DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('t')
                    ->where('t.user = :user')
                    ->andWhere('t.date BETWEEN :start AND :end')
                    ->setParameter('user', $user)
                    ->setParameter('start', $startDate)
                    ->setParameter('end', $endDate)
                    ->orderBy('t.date', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    public function sumByType(User $user, TransactionType $type): float {
        return $this->createQueryBuilder('t')
                    ->select('COALESCE(SUM(t.amount), 0)')
                    ->where('t.user = :user')
                    ->andWhere('t.type = :type')
                    ->setParameter('user', $user)
                    ->setParameter('type', $type)
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function sumByTypeAndPeriod(User               $user,
                                       \DateTimeInterface $startDate,
                                       \DateTimeInterface $endDate,
                                       TransactionType    $type): float {
        return $this->createQueryBuilder('t')
                    ->select('COALESCE(SUM(t.amount), 0)')
                    ->where('t.user = :user')
                    ->andWhere('t.type = :type')
                    ->andWhere('t.date BETWEEN :start AND :end')
                    ->setParameter('user', $user)
                    ->setParameter('type', $type)
                    ->setParameter('start', $startDate)
                    ->setParameter('end', $endDate)
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function countByPeriod(User               $user,
                                  \DateTimeInterface $startDate,
                                  \DateTimeInterface $endDate): int {
        return $this->createQueryBuilder('t')
                    ->select('COUNT(t.id)')
                    ->where('t.user = :user')
                    ->andWhere('t.date BETWEEN :start AND :end')
                    ->setParameter('user', $user)
                    ->setParameter('start', $startDate)
                    ->setParameter('end', $endDate)
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function sumByTransactionTypeAndCategory(User               $user,
                                                    TransactionType    $type,
                                                    \DateTimeInterface $startDate,
                                                    \DateTimeInterface $endDate): array {
        return $this->createQueryBuilder('t')
                    ->select('c.name AS categoryName, SUM(t.amount) AS totalAmount, c.id AS categoryId')
                    ->join('t.category', 'c')
                    ->where('t.user = :user')
                    ->andWhere('t.type = :type')
                    ->andWhere('t.date BETWEEN :start AND :end')
                    ->groupBy('c.id', 'c.name')
                    ->setParameter('user', $user)
                    ->setParameter('type', $type)
                    ->setParameter('start', $startDate)
                    ->setParameter('end', $endDate)
                    ->getQuery()
                    ->getArrayResult();
    }

    /**
     * Finds user transactions with filtering and pagination.
     *
     * @param User $user
     * @param TransactionFilterRequest $filters
     * @return Paginator<Transaction>
     */
    public function findPaginatedByUser(User $user, TransactionFilterRequest $filters): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('t')
                             ->where('t.user = :user')
                             ->setParameter('user', $user)
                             ->orderBy('t.' . $filters->sortBy, $filters->sortOrder);

        // Filters
        if ($filters->type) {
            $queryBuilder->andWhere('t.type = :type')
                         ->setParameter('type', TransactionType::from($filters->type));
        }

        if ($filters->accountId && Uuid::isValid($filters->accountId)) {
            $queryBuilder->andWhere('t.account = :accountId')
                         ->setParameter('accountId', Uuid::fromString($filters->accountId));
        }

        // Pagination
        $query = $queryBuilder->getQuery()
                              ->setMaxResults($filters->limit)
                              ->setFirstResult(($filters->page - 1) * $filters->limit);

        return new Paginator($query, fetchJoinCollection: false);
    }
}
