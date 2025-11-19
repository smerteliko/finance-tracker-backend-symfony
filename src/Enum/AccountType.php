<?php
namespace App\Enum;

enum AccountType: string {
    case CHECKING = 'CHECKING';
    case SAVINGS = 'SAVINGS';
    case CASH = 'CASH';
    case CREDIT_CARD = 'CREDIT_CARD';
    case INVESTMENT = 'INVESTMENT';

    public static function values(): array {
        return array_column(self::cases(), 'value');
    }

}
