<?php

namespace App\DTO\Category;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryRequest
{
    #[Assert\NotBlank(message: 'common.required_field')]
    #[OA\Property(description: 'Category name', example: 'Food & Dining')]
    public string $name;

    #[Assert\NotBlank(message: 'common.required_field')]
    #[OA\Property(description: 'Category color', example: '#FF6384')]
    public string $color;

    #[Assert\NotBlank(message: 'common.required_field')]
    #[Assert\Choice(['INCOME', 'EXPENSE'], message: 'validation.type_invalid')]
    #[OA\Property( description: 'Category type', enum: [ 'INCOME', 'EXPENSE'], example: 'EXPENSE' )]
    public string $type;

}
