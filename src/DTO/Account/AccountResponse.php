<?php

namespace App\DTO\Account;

final class AccountResponse
{
    public function __construct(
        public string $id,
        public string $name,
        public string $type,
        public float $balance,
        public ?string $currency,
        public string $userId,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}
}
