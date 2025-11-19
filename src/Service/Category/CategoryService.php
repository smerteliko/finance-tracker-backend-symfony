<?php

namespace App\Service\Category;

use App\DTO\Category\CategoryRequest;
use App\Entity\Category;
use App\Entity\User;
use App\Enum\TransactionType;
use App\Exception\ResourceNotFoundException;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

final class CategoryService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CategoryRepository $categoryRepository
    ) {}

    public function getCategoryById(string $id): Category
    {
        if (!Uuid::isValid($id)) {
            throw new ResourceNotFoundException(sprintf('Category ID "%s" is invalid UUID.', $id));
        }

        $category = $this->categoryRepository->find(Uuid::fromString($id));

        if (!$category) {
            throw new ResourceNotFoundException(sprintf('Category with ID %s not found.', $id));
        }

        return $category;
    }

    public function getCategoryList(User $user, ?TransactionType $type = null): array
    {
        $criteria = ['user' => $user];
        if ($type) {
            $criteria['type'] = $type;
        }

        return $this->categoryRepository->findBy($criteria);
    }

    public function createCategory(User $user, CategoryRequest $request): Category
    {
        $existingCategory = $this->categoryRepository->findOneBy([
                                                                     'user' => $user,
                                                                     'name' => $request->name,
                                                                     'type' => TransactionType::from($request->type),
                                                                 ]);

        if ($existingCategory) {
            throw new \InvalidArgumentException(sprintf(
                                                    'Category with name "%s" and type "%s" already exists.',
                                                    $request->name,
                                                    $request->type
                                                ));
        }

        $category = new Category();
        $category->setUser($user);
        $category->setName($request->name);
        $category->setType(TransactionType::from($request->type));
        $category->setColor($request->color);

        $this->categoryRepository->save($category, TRUE);

        return $category;
    }

    public function updateCategory(Category $category, CategoryRequest $request): Category
    {
        $category->setName($request->name);
        $category->setType(TransactionType::from($request->type));
        $category->setColor($request->color);

        $this->entityManager->flush();

        return $category;
    }

    public function deleteCategory(Category $category): void
    {
        if ($category->getTransactions()->count() > 0) {
            throw new \InvalidArgumentException('Cannot delete category linked to transactions.');
        }
        $this->categoryRepository->remove($category, TRUE);
    }
}
