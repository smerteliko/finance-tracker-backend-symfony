<?php

namespace App\Controller;

use App\DTO\Transaction\TransactionFilterRequest;
use App\DTO\Transaction\TransactionRequest;
use App\Entity\User;
use App\Service\Transaction\TransactionService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Security\TransactionVoter;

// Assume TransactionVoter is defined

#[Route( '/api/transactions', name: 'api_transactions_' )]
#[OA\Tag( name: 'Transactions' )]
#[Security( name: 'Bearer' )]
final class TransactionController extends AbstractController {
    public function __construct(private readonly TransactionService $transactionService) { }

    /**
     * Lists, filters, and paginates transactions.
     *
     * @throws \Exception
     */
    #[Route( '', name: 'list', methods: [ 'GET' ] )]
    #[OA\Parameter(
        name: 'sortBy',
        in: 'query',
        schema: new OA\Schema(
            type: 'string',
            default: 'transactionDate'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the paginated list of transactions',
        content: new OA\JsonContent(schema: '#/components/schemas/PaginatedResponse')
    )]
    public function listTransactions(#[CurrentUser] User                        $user,
                                     #[MapQueryString] TransactionFilterRequest $filters): JsonResponse {
        $paginatedResponse = $this->transactionService->getFilteredTransactions($user,
                                                                                $filters);
        return $this->json($paginatedResponse, Response::HTTP_OK);
    }

    /**
     * Creates a new transaction.
     *
     * @throws \Exception
     */
    #[Route( '', name: 'create', methods: [ 'POST' ] )]
    #[OA\RequestBody( content: new OA\JsonContent(schema: '#/components/schemas/TransactionRequest') )]
    #[OA\Response(
        response: 201,
        description: 'Transaction successfully created',
        content: new OA\JsonContent(schema: '#/components/schemas/Transaction')
    )]
    public function createTransaction(#[CurrentUser] User                     $user,
                                      #[MapRequestPayload] TransactionRequest $request): JsonResponse {
        $transaction = $this->transactionService->createTransaction($user,
                                                                    $request);

        return $this->json($transaction,
                           Response::HTTP_CREATED,
                           [],
                           [ 'groups' => [ 'transaction:read' ] ]);
    }

    /**
     * Gets a single transaction by ID.
     */
    #[Route( '/{id}', name: 'get', methods: [ 'GET' ] )]
    #[OA\Parameter(
        name: 'id',
        description: 'Transaction UUID',
        in: 'path',
        required: TRUE,
        schema: new OA\Schema(
            type: 'string',
            format: 'uuid'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the transaction details',
        content: new OA\JsonContent(schema: '#/components/schemas/Transaction')
    )]
    public function getTransaction(string              $id,
                                   #[CurrentUser] User $user): JsonResponse {
        $transaction = $this->transactionService->getOneTransactionById($id);
        $this->denyAccessUnlessGranted(TransactionVoter::VIEW, $transaction);

        return $this->json($transaction,
                           Response::HTTP_OK,
                           [],
                           [ 'groups' => [ 'transaction:read' ] ]);
    }

    /**
     * Updates an existing transaction.
     */
    #[Route( '/{id}', name: 'update', methods: [ 'PUT' ] )]
    #[OA\RequestBody( content: new OA\JsonContent(schema: '#/components/schemas/TransactionRequest') )]
    #[OA\Response(
        response: 200,
        description: 'Transaction successfully updated',
        content: new OA\JsonContent(schema: '#/components/schemas/Transaction')
    )]
    public function updateTransaction(string                                  $id,
                                      #[CurrentUser] User                     $user,
                                      #[MapRequestPayload] TransactionRequest $request): JsonResponse {
        $transaction = $this->transactionService->getOneTransactionById($id);
        $this->denyAccessUnlessGranted(TransactionVoter::EDIT, $transaction);

        $transaction = $this->transactionService->updateTransaction($transaction,
                                                                    $request);

        return $this->json($transaction,
                           Response::HTTP_OK,
                           [],
                           [ 'groups' => [ 'transaction:read' ] ]);
    }

    /**
     * Deletes a transaction.
     */
    #[Route( '/{id}', name: 'delete', methods: [ 'DELETE' ] )]
    #[OA\Response( response: 204, description: 'Transaction successfully deleted' )]
    public function deleteTransaction(string              $id,
                                      #[CurrentUser] User $user): JsonResponse {
        $transaction = $this->transactionService->getOneTransactionById($id);
        $this->denyAccessUnlessGranted(TransactionVoter::DELETE, $transaction);

        $this->transactionService->deleteTransaction($transaction);

        return $this->json(NULL, Response::HTTP_NO_CONTENT);
    }
}
