<?php

namespace App\Controller;

use App\DTO\Auth\LoginRequest;
use App\DTO\Auth\RegisterRequest;
use App\Entity\User;
use App\Service\Auth\AuthService;
use App\Service\JWT\JwtService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route( '/api/auth', name: 'api_auth_' )]
#[OA\Tag( name: 'Auth & User' )]
final class AuthController extends AbstractController {
    public function __construct(private readonly AuthService $authService,
                                private readonly JwtService  $jwtService) {
    }

    /**
     * Registers a new user account.
     */
    #[Route( '/register', name: 'register', methods: [ 'POST' ] )]
    #[OA\RequestBody( content: new OA\JsonContent(schema: '#/components/schemas/RegisterRequest') )]
    #[OA\Response( response: 201, description: 'User successfully registered', content: new OA\JsonContent(schema: '#/components/schemas/AuthResponse') )]
    #[OA\Response( response: 409, description: 'User with this email already exists' )]
    public function register(#[MapRequestPayload] RegisterRequest $request): JsonResponse {
        $user  = $this->authService->register($request);
        $token = $this->jwtService->generateToken($user);

        $authResponse = $this->authService->createAuthResponse($user, $token);

        return $this->json($authResponse, Response::HTTP_CREATED);
    }

    /**
     * Logs in a user.
     */
    #[Route( '/login', name: 'login', methods: [ 'POST' ] )]
    #[OA\RequestBody( content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest') )]
    #[OA\Response( response: 200, description: 'Successful login', content: new OA\JsonContent(schema: '#/components/schemas/AuthResponse') )]
    #[OA\Response( response: 401, description: 'Invalid credentials' )]
    public function login(#[MapRequestPayload] LoginRequest $request): JsonResponse {
        $user  = $this->authService->verifyCredentials($request);
        $token = $this->jwtService->generateToken($user);

        $authResponse = $this->authService->createAuthResponse($user, $token);

        return $this->json(
            $authResponse,
            Response::HTTP_OK,
        );
    }

    /**
     * Gets the profile of the currently authenticated user.
     */
    #[Route( '/me', name: 'me', methods: [ 'GET' ] )]
    #[Security( name: 'Bearer' )]
    #[OA\Response( response: 200, description: 'Returns the user profile', content: new OA\JsonContent(schema: '#/components/schemas/User') )]
    #[OA\Response( response: 401, description: 'JWT Token missing or invalid' )]
    public function me(#[CurrentUser] User $user): JsonResponse {
        return $this->json($user,
                           Response::HTTP_OK,
                           [],
                           [ 'groups' => [ 'user:read' ] ]);
    }
}
