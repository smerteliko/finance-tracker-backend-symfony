<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function save(Transaction $transaction, bool $flush = false): void
    {
        $this->getEntityManager()->persist($transaction);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $transaction, bool $flush = false): void
    {
        $this->getEntityManager()->remove($transaction);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUserAndFilters(User $user, array $filters = []): array
    {
        $qb = $this->createQueryBuilder('t')
                   ->leftJoin('t.category', 'c')
                   ->addSelect('c')
                   ->andWhere('t.user = :user')
                   ->setParameter('user', $user)
                   ->orderBy('t.date', 'DESC');

        if (isset($filters['startDate'])) {
            $qb->andWhere('t.date >= :startDate')
               ->setParameter('startDate', $filters['startDate']);
        }

        if (isset($filters['endDate'])) {
            $qb->andWhere('t.date <= :endDate')
               ->setParameter('endDate', $filters['endDate']);
        }

        if (isset($filters['type'])) {
            $qb->andWhere('t.type = :type')
               ->setParameter('type', $filters['type']);
        }

        if (isset($filters['categoryId'])) {
            $qb->andWhere('t.category = :categoryId')
               ->setParameter('categoryId', $filters['categoryId']);
        }

        return $qb->getQuery()->getResult();
    }

    public function findFilteredTransactions(User $user, array $filters): array
    {
        $qb = $this->createQueryBuilder('t')
                   ->leftJoin('t.category', 'c')
                   ->addSelect('c')
                   ->andWhere('t.user = :user')
                   ->setParameter('user', $user)
                   ->orderBy('t.date', 'DESC');

        if (isset($filters['startDate'])) {
            $qb->andWhere('t.date >= :startDate')
               ->setParameter('startDate', $filters['startDate']);
        }

        if (isset($filters['endDate'])) {
            $qb->andWhere('t.date <= :endDate')
               ->setParameter('endDate', $filters['endDate']);
        }

        if (isset($filters['type'])) {
            $qb->andWhere('t.type = :type')
               ->setParameter('type', $filters['type']);
        }

        if (isset($filters['categoryId'])) {
            $qb->andWhere('t.category = :categoryId')
               ->setParameter('categoryId', $filters['categoryId']);
        }

        // Pagination
        $page = $filters['page'] ?? 0;
        $size = $filters['size'] ?? 10;
        $qb->setFirstResult($page * $size)
           ->setMaxResults($size);

        return $qb->getQuery()->getResult();
    }

    public function findByUserAndDateRange(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('t')
                    ->leftJoin('t.category', 'c')
                    ->addSelect('c')
                    ->andWhere('t.user = :user')
                    ->andWhere('t.date >= :startDate')
                    ->andWhere('t.date <= :endDate')
                    ->setParameter('user', $user)
                    ->setParameter('startDate', $startDate)
                    ->setParameter('endDate', $endDate)
                    ->orderBy('t.date', 'DESC')
                    ->getQuery()
                    ->getResult();
    }

    public function getTotalCountByUserAndFilters(User $user, array $filters = []): int
    {
        $qb = $this->createQueryBuilder('t')
                   ->select('COUNT(t.id)')
                   ->andWhere('t.user = :user')
                   ->setParameter('user', $user);

        if (isset($filters['startDate'])) {
            $qb->andWhere('t.date >= :startDate')
               ->setParameter('startDate', $filters['startDate']);
        }

        if (isset($filters['endDate'])) {
            $qb->andWhere('t.date <= :endDate')
               ->setParameter('endDate', $filters['endDate']);
        }

        if (isset($filters['type'])) {
            $qb->andWhere('t.type = :type')
               ->setParameter('type', $filters['type']);
        }

        if (isset($filters['categoryId'])) {
            $qb->andWhere('t.category = :categoryId')
               ->setParameter('categoryId', $filters['categoryId']);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
