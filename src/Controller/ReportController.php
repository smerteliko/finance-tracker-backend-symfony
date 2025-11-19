<?php

namespace App\Controller;

use App\DTO\Analytics\AnalyticsRequest;
use App\Entity\User;
use App\Service\Reports\ReportService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/reports', name: 'api_reports_')]
#[OA\Tag(name: 'Reports')]
#[Security(name: 'Bearer')]
final class ReportController extends AbstractController
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Generates and returns a financial report in CSV format for a given
     * period.
     *
     * @throws \Exception
     */
    #[Route('/csv', name: 'generate_csv', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\JsonContent(schema: '#/components/schemas/AnalyticsRequest'))]
    #[OA\Response(response: 200, description: 'Returns the report as a CSV file', content: new OA\MediaType(mediaType: 'text/csv'))]
    public function generateCsv(
        #[CurrentUser] User $user,
        #[MapRequestPayload] AnalyticsRequest $request
    ): Response {

        $csvContent = $this->reportService->generateCsvReport($user, $request->startDate, $request->endDate);
        $fileName = sprintf(
            'finance_report_%s_to_%s.csv',
            (new \DateTimeImmutable($request->startDate))->format('Ymd'),
            (new \DateTimeImmutable($request->endDate))->format('Ymd'));

        $response = new Response($csvContent);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $fileName));

        return $response;
    }/**/

    /**
     * Generates and returns a brief text summary of the financial data for a
     * period.
     *
     * @throws \Exception
     */
    #[Route('/summary', name: 'generate_summary', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\JsonContent(schema: '#/components/schemas/AnalyticsRequest'))]
    #[OA\Response(
        response: 200,
        description: 'Returns the text summary',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'summary',
                    type: 'string',
                    example: 'Total Income: 1200.00\nTotal Expense: 800.00\n...'
                )
            ]
        )
    )]
    public function generateSummary(
        #[CurrentUser] User $user,
        #[MapRequestPayload] AnalyticsRequest $request
    ): JsonResponse {

        $summary = $this->reportService->generateTextSummary($user, $request->startDate, $request->endDate);

        return $this->json(['summary' => $summary], Response::HTTP_OK);
    }
}
