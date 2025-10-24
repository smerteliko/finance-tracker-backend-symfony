<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $category, bool $flush = false): void
    {
        $this->getEntityManager()->persist($category);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $category, bool $flush = false): void
    {
        $this->getEntityManager()->remove($category);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUserAndType(User $user, string $type): array
    {
        return $this->createQueryBuilder('c')
                    ->andWhere('c.user = :user')
                    ->andWhere('c.type = :type')
                    ->setParameter('user', $user)
                    ->setParameter('type', strtoupper($type))
                    ->getQuery()
                    ->getResult();
    }

    public function findDefaultCategoriesForUser(User $user): array
    {
        $defaultCategories = [
            ['name' => 'Salary', 'type' => 'INCOME', 'color' => '#10b981'],
            ['name' => 'Freelance', 'type' => 'INCOME', 'color' => '#3b82f6'],
            ['name' => 'Investments', 'type' => 'INCOME', 'color' => '#8b5cf6'],
            ['name' => 'Food', 'type' => 'EXPENSE', 'color' => '#ef4444'],
            ['name' => 'Transport', 'type' => 'EXPENSE', 'color' => '#f59e0b'],
            ['name' => 'Entertainment', 'type' => 'EXPENSE', 'color' => '#ec4899'],
            ['name' => 'Utilities', 'type' => 'EXPENSE', 'color' => '#6b7280'],
            ['name' => 'Healthcare', 'type' => 'EXPENSE', 'color' => '#84cc16'],
        ];

        $categories = [];
        foreach ($defaultCategories as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $category->setType($categoryData['type']);
            $category->setColor($categoryData['color']);
            $category->setUser($user);
            $categories[] = $category;
        }

        return $categories;
    }
}
