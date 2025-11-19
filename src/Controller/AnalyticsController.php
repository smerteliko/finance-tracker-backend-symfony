<?php

namespace App\Controller;

use App\DTO\Analytics\AnalyticsRequest;
use App\Entity\User;
use App\Service\Analytics\AnalyticsService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route( '/api/analytics', name: 'api_analytics_' )]
#[OA\Tag( name: 'Analytics' )]
#[Security( name: 'Bearer' )]
final class AnalyticsController extends AbstractController {
    public function __construct(private readonly AnalyticsService $analyticsService) { }

    /**
     * Returns the user's current overall balance (all-time).
     */
    #[Route( '/balance', name: 'get_balance', methods: [ 'GET' ] )]
    #[OA\Response(
        response: 200,
        description: 'Returns the current overall balance',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'currentBalance',
                    type: 'number',
                    format: 'float',
                    example: 520.45
                )
            ]
        )
    )]
    public function getCurrentBalance(#[CurrentUser] User $user): JsonResponse {
        $balance = $this->analyticsService->getCurrentBalance($user);

        return $this->json([ 'currentBalance' => $balance ],
                           Response::HTTP_OK);
    }

    /**
     * Returns detailed financial analytics (income, expense, breakdown) for a
     * given period.
     *
     * @throws \Exception
     */
    #[Route( '/', name: 'get_analytics', methods: [ 'POST' ] )]
    #[OA\RequestBody( content: new OA\JsonContent(ref: '#/components/schemas/AnalyticsRequest') )]
    #[OA\Response(
        response: 200,
        description: 'Returns detailed financial analytics',
        content: new OA\JsonContent(schema: '#/components/schemas/AnalyticsResponse')
    )]
    public function getAnalyticsForPeriod(#[CurrentUser] User $user,
                                          #[MapRequestPayload] AnalyticsRequest $request): JsonResponse {

        $analyticsResponse = $this->analyticsService->getAnalyticsForPeriod($user,
                                                                            $request->startDate,
                                                                            $request->endDate);

        return $this->json($analyticsResponse, Response::HTTP_OK);
    }
}
