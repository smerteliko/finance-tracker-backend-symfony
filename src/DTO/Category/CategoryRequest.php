<?php

namespace App\DTO\Category;

use App\Enum\TransactionType;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(title: 'CategoryRequest', description: 'Data required to create or update a category.')]
final class CategoryRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Category name cannot be blank.')]
        #[Assert\Length(min: 3, max: 255)]
        #[OA\Property( description: 'The name of the category.', type: 'string', example: 'Groceries' )]
        public readonly ?string $name,

        #[Assert\NotBlank(message: 'Category type cannot be blank.')]
        #[Assert\Choice(callback: [TransactionType::class, 'values'], message: 'Type must be either INCOME or EXPENSE.')]
        #[OA\Property( description: 'The transaction type associated with the category.', type: 'string', enum: [ 'INCOME', 'EXPENSE'] )]
        public readonly ?string $type,

        #[Assert\Regex(pattern: '/^#[0-9a-fA-F]{6}$/', message: 'Color must be a valid 6-digit hex code like #RRGGBB.')]
        #[OA\Property( description: 'Optional hex color code for the category.', type: 'string', example: '#FF5733', nullable: true )]
        public readonly ?string $color = null,
    ) {}
}
