<?php

namespace App\DTO\Transaction;

use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\TransactionType;

final class TransactionFilterRequest
{
    public function __construct(
        #[Assert\Positive]
        public readonly int $page = 1,

        #[Assert\Range(min: 1, max: 100)]
        public readonly int $limit = 10,

        #[Assert\Choice(['createdAt', 'date', 'amount'])]
        public readonly string $sortBy = 'date',

        #[Assert\Choice(['asc', 'desc'])]
        public readonly string $sortOrder = 'desc',

        #[Assert\Uuid] // Предполагаем, что Account ID - это UUID
        public readonly ?string $accountId = null,

        #[Assert\Choice(callback: [TransactionType::class, 'values'])]
        public readonly ?string $type = null,
    ) {}
}
