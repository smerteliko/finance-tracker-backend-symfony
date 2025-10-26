<?php

namespace App\Controller;

use App\DTO\Error\ErrorResponse;
use App\DTO\Transaction\PaginatedResponse;
use App\DTO\Transaction\TransactionFilterRequest;
use App\DTO\Transaction\TransactionRequest;
use App\DTO\Transaction\TransactionResponse;
use App\Entity\Transaction;
use App\Service\Transaction\TransactionService;
use App\Service\MapperServices\MapperFacade;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/transactions')]
#[OA\Tag(name: 'transactions')]
class TransactionController extends AbstractController
{
    public function __construct(
        private TransactionService $transactionService,
        private MapperFacade $mapper
    ) {}

    /**
     * @throws ExceptionInterface
     */
    #[Route('', methods: [ 'POST'])]
    #[OA\RequestBody(
        content: new Model(type: TransactionRequest::class)
    )]
    #[OA\Response(
        response: 201,
        description: 'Transaction created successfully',
        content: new Model(type: TransactionResponse::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation error',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized'
    )]
    #[Security(name: 'Bearer')]
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        #[CurrentUser] $user
    ): JsonResponse {

        $transactionRequest = $serializer->deserialize(
            $request->getContent(),
            TransactionRequest::class,
            'json'
        );
        $transactionRequest->userId = $user->getId();

        $errors = $validator->validate($transactionRequest);
        if (count($errors) > 0) {
            $errorResponse = new ErrorResponse(
                error: (string) $errors,
                code: 400,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 400);
        }

        try {
            $transaction = $this->transactionService->createTransaction($transactionRequest);
            $transactionResponse = $this->mapper->mapToDto($transaction, TransactionResponse::class);
            return $this->json($transactionResponse, 201);
        } catch (\Exception $e) {
            $errorResponse = new ErrorResponse(
                error: $e->getMessage(),
                code: 400,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 400);
        }
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Parameter(
        name: 'id',
        description: 'Transaction ID',
        in: 'path',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Transaction details',
        content: new Model(type: TransactionResponse::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Transaction not found'
    )]
    #[Security(name: 'Bearer')]
    public function show(
        int $id,
        #[CurrentUser] $user
    ): JsonResponse {
        try {
            $transaction = $this->transactionService->getUserTransactions($user, []);
            $transactionResponse = $this->mapper->mapToDto((object)$transaction, TransactionResponse::class);
            return $this->json($transactionResponse);
        } catch (\Exception $e) {
            $errorResponse = new ErrorResponse(
                error: $e->getMessage(),
                code: 404,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 404);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[OA\Parameter(
        name: 'id',
        description: 'Transaction ID',
        in: 'path',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        content: new Model(type: TransactionRequest::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Transaction updated successfully',
        content: new Model(type: TransactionResponse::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Transaction not found'
    )]
    #[Security(name: 'Bearer')]
    public function update(
        int $id,
        Request $request,
        ValidatorInterface $validator,
        #[CurrentUser] $user
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $transactionRequest = new TransactionRequest();
        $transactionRequest->amount = $data['amount'] ?? '';
        $transactionRequest->type = $data['type'] ?? '';
        $transactionRequest->description = $data['description'] ?? null;
        $transactionRequest->date = $data['date'] ?? '';
        $transactionRequest->categoryId = $data['categoryId'] ?? 0;
        $transactionRequest->userId = $user->getId();

        $errors = $validator->validate($transactionRequest);
        if (count($errors) > 0) {
            $errorResponse = new ErrorResponse(
                error: (string) $errors,
                code: 400,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 400);
        }

        try {
            $transaction = $this->transactionService->updateTransaction($id, $user, $transactionRequest);
            $transactionResponse = $this->mapper->mapToDto($transaction, TransactionResponse::class);
            return $this->json($transactionResponse);
        } catch (\Exception $e) {
            $errorResponse = new ErrorResponse(
                error: $e->getMessage(),
                code: 404,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 404);
        }
    }

//    #[Route('/{id}', methods: ['DELETE'])]
//    #[OA\Parameter(
//        name: 'id', description: 'Transaction ID', in: 'path', schema: new OA\Schema(type: 'integer')
//    )]
//    #[OA\Response(
//        response: 200,
//        description: 'Transaction deleted successfully',
//        content: new Model(type: SuccessResponse::class)
//    )]
//    #[OA\Response(
//        response: 404,
//        description: 'Transaction not found',
//        content: new Model(type: ErrorResponse::class)
//    )]
//    #[OA\Security(name: 'Bearer')]
//    public function delete(
//        int $id,
//        #[CurrentUser] $user
//    ): JsonResponse {
//        try {
//            $this->transactionService->deleteTransaction($id, $user);
//            $successResponse = new SuccessResponse('Transaction deleted successfully');
//            return $this->json($successResponse);
//        } catch (\Exception $e) {
//            $errorResponse = new ErrorResponse(
//                error: $e->getMessage(),
//                code: 404,
//                timestamp: new \DateTimeImmutable()
//            );
//            return $this->json($errorResponse, 404);
//        }
//    }

    #[Route('', methods: ['GET'])]
    #[OA\Parameter(
        name: 'page',
        description: 'Page number',
        in: 'query',
        schema: new OA\Schema(
            type: 'integer',
            default: 0
        )
    )]
    #[OA\Parameter(
        name: 'size',
        description: 'Page size',
        in: 'query',
        schema: new OA\Schema(
            type: 'integer',
            default: 20
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'List of user transactions',
        content: new Model(type: PaginatedResponse::class)
    )]
    #[Security(name: 'Bearer')]
    public function list(
        Request $request,
        #[CurrentUser] $user
    ): JsonResponse {
        try {
            $page = (int) $request->query->get('page', 0);
            $size = (int) $request->query->get('size', 20);

            $result = $this->transactionService->getFilteredTransactions($user, [
                'page' => $page,
                'size' => $size
            ]);

            $transactionResponses = $this->mapper->mapToDtoArray($result['content'], TransactionResponse::class);

            $paginatedData = [
                'content' => $transactionResponses,
                'currentPage' => $result['currentPage'],
                'totalPages' => $result['totalPages'],
                'totalElements' => $result['totalElements'],
                'size' => $result['size']
            ];

            $paginatedResponse = $this->mapper->mapToPaginatedResponse($paginatedData);
            return $this->json($paginatedResponse);
        } catch (\Exception $e) {
            $errorResponse = new ErrorResponse(
                error: $e->getMessage(),
                code: 400,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 400);
        }
    }

    #[Route('/filter', methods: ['POST'])]
    #[OA\RequestBody(
        content: new Model(type: TransactionFilterRequest::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Filtered transactions with pagination',
        content: new Model(type: PaginatedResponse::class)
    )]
    #[Security(name: 'Bearer')]
    public function filter(
        Request $request,
        #[CurrentUser] $user
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);


        $filterRequest = new TransactionFilterRequest();
        $filterRequest->page = $data['page'] ?? 0;
        $filterRequest->size = $data['size'] ?? 10;
        $filterRequest->startDate = $data['startDate'] ?? null;
        $filterRequest->endDate = $data['endDate'] ?? null;
        $filterRequest->type = $data['type'] ?? null;
        $filterRequest->categoryId = $data['categoryId'] ?? null;
        $filterRequest->minAmount = $data['minAmount'] ?? null;
        $filterRequest->maxAmount = $data['maxAmount'] ?? null;

        try {
            $filters = (array) $filterRequest;
            $filters = array_filter($filters, fn($value) => $value !== null);

            $result = $this->transactionService->getFilteredTransactions($user, $filters);

            $transactionResponses = $this->mapper->mapToDtoArray($result['content'], TransactionResponse::class);

            $paginatedData = [
                'content' => $transactionResponses,
                'currentPage' => $result['currentPage'],
                'totalPages' => $result['totalPages'],
                'totalElements' => $result['totalElements'],
                'size' => $result['size']
            ];

            $paginatedResponse = $this->mapper->mapToPaginatedResponse($paginatedData);
            return $this->json($paginatedResponse);
        } catch (\Exception $e) {
            $errorResponse = new ErrorResponse(
                error: $e->getMessage(),
                code: 400,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 400);
        }
    }

    #[Route('/period', methods: ['GET'])]
    #[OA\Parameter(
        name: 'startDate',
        description: 'Start date (ISO 8601)',
        in: 'query',
        required: true,
        schema: new OA\Schema(
            type: 'string', format: 'date-time'
        )
    )]
    #[OA\Parameter(
        name: 'endDate',
        description: 'End date (ISO 8601)',
        in: 'query',
        required: true,
        schema: new OA\Schema(
            type: 'string',
            format: 'date-time'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Transactions for the specified period',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: TransactionResponse::class))
        )
    )]
    #[Security(name: 'Bearer')]
    public function getByPeriod(
        Request $request,
        #[CurrentUser] $user
    ): JsonResponse {
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        if (!$startDate || !$endDate) {
            $errorResponse = new ErrorResponse(
                error: 'Start date and end date are required',
                code: 400,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 400);
        }

        try {
            $transactions = $this->transactionService->getUserTransactions($user, [
                'startDate' => new \DateTimeImmutable($startDate),
                'endDate' => new \DateTimeImmutable($endDate)
            ]);

            $transactionResponses = $this->mapper->mapToDtoArray($transactions, TransactionResponse::class);
            return $this->json($transactionResponses);
        } catch (\Exception $e) {
            $errorResponse = new ErrorResponse(
                error: $e->getMessage(),
                code: 400,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 400);
        }
    }

    #[Route('/recent', methods: ['GET'])]
    #[OA\Parameter(
        name: 'limit',
        description: 'Number of recent transactions',
        in: 'query',
        schema: new OA\Schema(
            type: 'integer', default: 10
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Recent transactions',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: TransactionResponse::class))
        )
    )]
    #[Security(name: 'Bearer')]
    public function recent(
        Request $request,
        #[CurrentUser] $user
    ): JsonResponse {
        try {
            $limit = (int) $request->query->get('limit', 10);
            $transactions = $this->transactionService->getRecentTransactions($user, $limit);
            $transactionResponses = $this->mapper->mapToDtoArray($transactions, TransactionResponse::class);
            return $this->json($transactionResponses);
        } catch (\Exception $e) {
            $errorResponse = new ErrorResponse(
                error: $e->getMessage(),
                code: 400,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 400);
        }
    }

    #[Route('/search', methods: ['GET'])]
    #[OA\Parameter(
        name: 'q',
        description: 'Search query',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'Page number',
        in: 'query',
        schema: new OA\Schema(
            type: 'integer',
            default: 0
        )
    )]
    #[OA\Parameter(
        name: 'size',
        description: 'Page size',
        in: 'query',
        schema: new OA\Schema(
            type: 'integer',
            default: 20
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Search results',
        content: new Model(type: PaginatedResponse::class)
    )]
    #[Security(name: 'Bearer')]
    public function search(
        Request $request,
        #[CurrentUser] $user
    ): JsonResponse {
        $query = $request->query->get('q');
        $page = (int) $request->query->get('page', 0);
        $size = (int) $request->query->get('size', 20);

        if (!$query) {
            $errorResponse = new ErrorResponse(
                error: 'Search query is required',
                code: 400,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 400);
        }

        try {
            $result = $this->transactionService->searchTransactions($user, $query, $page, $size);
            $transactionResponses = $this->mapper->mapToDtoArray($result['content'], TransactionResponse::class);

            $paginatedData = [
                'content' => $transactionResponses,
                'currentPage' => $result['currentPage'],
                'totalPages' => $result['totalPages'],
                'totalElements' => $result['totalElements'],
                'size' => $result['size']
            ];

            $paginatedResponse = $this->mapper->mapToPaginatedResponse($paginatedData);
            return $this->json($paginatedResponse);
        } catch (\Exception $e) {
            $errorResponse = new ErrorResponse(
                error: $e->getMessage(),
                code: 400,
                timestamp: new \DateTimeImmutable()
            );
            return $this->json($errorResponse, 400);
        }
    }
}
