<?php

namespace App\Controller;

use App\DTO\Account\AccountRequest;
use App\Entity\User;
use App\Entity\Account;
use App\Service\Account\AccountService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Security\AccountVoter;

#[Route( '/api/accounts', name: 'api_accounts_' )]
#[OA\Tag( name: 'Accounts' )]
#[Security( name: 'Bearer' )]
final class AccountController extends AbstractController {
    public function __construct(private readonly AccountService $accountService) { }

    #[Route( '', name: 'list', methods: [ 'GET' ] )]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of accounts',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(schema: '#/components/schemas/Account')
        )
    )]
    public function listAccounts(#[CurrentUser] User $user): JsonResponse {
        $accounts = $this->accountService->getAllAccounts($user);

        return $this->json($accounts,
                           Response::HTTP_OK,
                           [],
                           [ 'groups' => [ 'account:read' ] ]);
    }

    /**
     * Creates a new financial account.
     */
    #[Route( '', name: 'create', methods: [ 'POST' ] )]
    #[OA\RequestBody(
        content: new OA\JsonContent(ref: '#/components/schemas/AccountRequest')
    )]
    #[OA\Response(
        response: 201,
        description: 'Account successfully created',
        content: new OA\JsonContent(schema: '#/components/schemas/Account')
    )]
    public function createAccount(#[CurrentUser] User                 $user,
                                  #[MapRequestPayload] AccountRequest $request): JsonResponse {
        $account = $this->accountService->createAccount($user, $request);

        return $this->json($account,
                           Response::HTTP_CREATED,
                           [],
                           [ 'groups' => [ 'account:read' ] ]);
    }

    /**
     * Gets a single account by ID.
     */
    #[Route( '/{id}', name: 'get', methods: [ 'GET' ] )]
    #[OA\Parameter(
        name: 'id',
        description: 'Account UUID',
        in: 'path',
        required: TRUE,
        schema: new OA\Schema(
            type: 'string',
            format: 'uuid'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the account details',
        content: new OA\JsonContent(schema: '#/components/schemas/Account')
    )]
    #[OA\Response(
        response: 403,
        description: 'Access Denied'
    )]
    #[OA\Response(
        response: 404,
        description: 'Resource Not Found'
    )]
    public function getAccount(string $id): JsonResponse {
        $account = $this->accountService->getAccountById($id);

        $this->denyAccessUnlessGranted(AccountVoter::VIEW, $account);

        return $this->json($account,
                           Response::HTTP_OK,
                           [],
                           [ 'groups' => [ 'account:read' ] ]);
    }

    /**
     * Updates an existing account's metadata.
     */
    #[Route( '/{id}', name: 'update', methods: [ 'PUT' ] )]
    #[OA\RequestBody( content: new OA\JsonContent(ref: '#/components/schemas/AccountRequest') )]
    #[OA\Response(
        response: 200,
        description: 'Account successfully updated',
        content: new OA\JsonContent(schema: '#/components/schemas/Account')
    )]
    public function updateAccount(string                              $id,
                                  #[MapRequestPayload] AccountRequest $request): JsonResponse {
        $account = $this->accountService->getAccountById($id);
        $this->denyAccessUnlessGranted(AccountVoter::EDIT, $account);

        $account = $this->accountService->updateAccount($account, $request);

        return $this->json($account,
                           Response::HTTP_OK,
                           [],
                           [ 'groups' => [ 'account:read' ] ]);
    }

    /**
     * Deletes an account.
     */
    #[Route( '/{id}', name: 'delete', methods: [ 'DELETE' ] )]
    #[OA\Response( response: 204, description: 'Account successfully deleted' )]
    #[OA\Response( response: 400, description: 'Account has associated transactions' )]
    public function deleteAccount(string $id): JsonResponse {
        $account = $this->accountService->getAccountById($id);
        $this->denyAccessUnlessGranted(AccountVoter::DELETE, $account);

        $this->accountService->deleteAccount($account);

        return $this->json(NULL, Response::HTTP_NO_CONTENT);
    }
}
