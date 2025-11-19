<?php

namespace App\Controller;

use App\DTO\Category\CategoryRequest;
use App\Entity\User;
use App\Service\Category\CategoryService;
use App\Security\CategoryVoter;
use App\Enum\TransactionType;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/categories', name: 'api_categories_')]
#[OA\Tag(name: 'Categories')]
#[Security(name: 'Bearer')]
final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    /**
     * Lists all categories for the authenticated user, optionally filtered by type.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Parameter(
        name: 'type',
        description: 'Filter by transaction type',
        in: 'query',
        schema: new OA\Schema(
            type: 'string',
            enum: [ 'INCOME', 'EXPENSE']
        )
    )]
    #[OA\Response(response: 200,
        description: 'Returns the list of categories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(schema: '#/components/schemas/Category')
        )
    )]
    public function listCategories(#[CurrentUser] User $user, ?string $type = null): JsonResponse
    {
        $transactionType = $type ? TransactionType::tryFrom(strtoupper($type)) : null;

        $categories = $this->categoryService->getCategoryList($user, $transactionType);

        return $this->json($categories, Response::HTTP_OK, [], ['groups' => ['category:read']]);
    }

    /**
     * Creates a new category.
     */
    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\JsonContent(schema: '#/components/schemas/CategoryRequest'))]
    #[OA\Response(response: 201, description: 'Category successfully created', content: new OA\JsonContent(schema: '#/components/schemas/Category'))]
    public function createCategory(
        #[CurrentUser] User $user,
        #[MapRequestPayload] CategoryRequest $request
    ): JsonResponse {
        $category = $this->categoryService->createCategory($user, $request);

        return $this->json($category, Response::HTTP_CREATED, [], ['groups' => ['category:read']]);
    }

    /**
     * Gets a single category by ID.
     */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    #[OA\Parameter(name: 'id', description: 'Category UUID', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))]
    #[OA\Response(response: 200, description: 'Returns the category details', content: new OA\JsonContent(schema: '#/components/schemas/Category'))]
    #[OA\Response(response: 403, description: 'Access Denied')]
    #[OA\Response(response: 404, description: 'Resource Not Found')]
    public function getCategory(string $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);

        // Authorization check via Voter
        $this->denyAccessUnlessGranted(CategoryVoter::VIEW, $category);

        return $this->json($category, Response::HTTP_OK, [], ['groups' => ['category:read']]);
    }

    /**
     * Updates an existing category.
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\RequestBody(content: new OA\JsonContent(schema: '#/components/schemas/CategoryRequest'))]
    #[OA\Response(response: 200, description: 'Category successfully updated', content: new OA\JsonContent(schema: '#/components/schemas/Category'))]
    public function updateCategory(
        string $id,
        #[MapRequestPayload] CategoryRequest $request
    ): JsonResponse {
        $category = $this->categoryService->getCategoryById($id);
        $this->denyAccessUnlessGranted(CategoryVoter::EDIT, $category);

        $category = $this->categoryService->updateCategory($category, $request);

        return $this->json($category, Response::HTTP_OK, [], ['groups' => ['category:read']]);
    }

    /**
     * Deletes a category.
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Parameter(name: 'id', description: 'Category UUID', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))]
    #[OA\Response(response: 204, description: 'Category successfully deleted')]
    #[OA\Response(response: 400, description: 'Category has associated transactions')]
    public function deleteCategory(string $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        $this->denyAccessUnlessGranted(CategoryVoter::DELETE, $category);

        $this->categoryService->deleteCategory($category);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
