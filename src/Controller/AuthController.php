<?php

namespace App\Controller;

use App\DTO\Auth\AuthResponse;
use App\DTO\Auth\LoginRequest;
use App\DTO\Auth\RegisterRequest;
use App\DTO\Error\ErrorResponse;
use App\Service\Auth\AuthService;
use App\Service\MapperServices\MapperFacade;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private  AuthService $authService,
        private  MapperFacade $mapper
    ) { }

    /**
     * @throws \JsonException
     */
    #[Route('/register', name: 'auth_register', methods: [ 'POST'])]
    #[OA\RequestBody(
        content: new Model(type: RegisterRequest::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'User registered successfully',
        content: new OA\JsonContent(
            properties: [
                            new OA\Property(property: 'token', type: 'string'),
                            new OA\Property(property: 'userId', type: 'integer'),
                            new OA\Property(property: 'firstName', type: 'string'),
                            new OA\Property(property: 'lastName', type: 'string'),
                            new OA\Property(property: 'email', type: 'string')
                        ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation error',
        content: new OA\JsonContent(
            properties: [
                            new OA\Property(property: 'error', type: 'string')
                        ]
        )
    )]
    public function register(
        Request $request,
        AuthService $authService,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(),
                            TRUE,
                            512,
                            JSON_THROW_ON_ERROR);

        $registerRequest = new RegisterRequest();
        $registerRequest->firstName = $data['firstName'] ?? '';
        $registerRequest->lastName = $data['lastName'] ?? '';
        $registerRequest->email = $data['email'] ?? '';
        $registerRequest->password = $data['password'] ?? '';

        $errors = $validator->validate($registerRequest);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 400);
        }

        try {
            $result = $authService->register($registerRequest);
            return $this->json($result);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }


    #[Route('/login', name: 'auth_login', methods: ['POST'])]
    #[OA\RequestBody(
        content: new Model(type: LoginRequest::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Login successful',
        content: new Model(type: AuthResponse::class)
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
        content: new Model(type: ErrorResponse::class)
    )]
    public function login(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $loginRequest = new LoginRequest();
        $loginRequest->email = $data['email'] ?? '';
        $loginRequest->password = $data['password'] ?? '';

        $errors = $validator->validate($loginRequest);
        if (count($errors) > 0) {
            $errorResponse = new ErrorResponse(
                error: 'Error processing login',
                code: 400,timestamp: (new \DateTimeImmutable())->format('Y-m-d\TH:i:s\Z')
            );
            return $this->json($errorResponse, 400);
        }

        try {
            $result = $this->authService->login($loginRequest);
            $user = $this->authService->getUserByEmail($loginRequest->email);
            $authResponse = $this->mapper->mapToAuthResponse($user, $result['token']);

            return $this->json($authResponse);
        } catch (\Exception $e) {
            $errorResponse = new ErrorResponse(
                error: 'Invalid credentials',
                code: 401, timestamp: (new \DateTimeImmutable())->format('Y-m-d\TH:i:s\Z')
            );
            return $this->json($errorResponse, 401);
        }
    }

}
