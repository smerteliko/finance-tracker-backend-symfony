<?php

namespace App\Enum;

enum TransactionType: string {
    case INCOME = 'INCOME';
    case EXPENSE = 'EXPENSE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
