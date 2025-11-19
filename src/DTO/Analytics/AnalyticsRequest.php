<?php

namespace App\DTO\Analytics;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class AnalyticsRequest
{
    #[OA\Property( type: 'string', format: 'date-time', example: '2025-10-01T00:00:00Z', nullable: true )]
    public readonly ?string $startDate;

    #[OA\Property( type: 'string', format: 'date-time', example: '2025-10-31T23:59:59Z', nullable: true )]
    public readonly ?string $endDate;

    public function __construct(
        ?string $startDate = null,
        ?string $endDate = null
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}
