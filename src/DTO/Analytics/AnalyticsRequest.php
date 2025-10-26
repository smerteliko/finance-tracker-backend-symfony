<?php

namespace App\DTO\Analytics;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class AnalyticsRequest
{
    #[Assert\NotBlank(message: 'common.required_field')]
    #[Assert\DateTime(message: 'validation.date_invalid')]
    #[OA\Property(description: 'Start date for analytics', example: '2024-01-01T00:00:00Z')]
    public string $startDate;

    #[Assert\NotBlank(message: 'common.required_field')]
    #[Assert\DateTime(message: 'validation.date_invalid')]
    #[OA\Property(description: 'End date for analytics', example: '2024-01-31T23:59:59Z')]
    public string $endDate;
}
