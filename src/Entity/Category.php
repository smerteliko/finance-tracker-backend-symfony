<?php

namespace App\Entity;

use App\Enum\TransactionType;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'categories',options: ['comment'=>'Financial category used to group transactions.'])]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'idx_categories_user_id', columns: ['user_id'])]
#[ORM\Index(name: 'idx_categories_type', columns: ['type'])]
#[ORM\Index(name: 'idx_categories_user_type', columns: ['user_id', 'type'])]
#[OA\Schema(
    schema: 'Category', title: 'Category', description: 'Financial category used to group transactions.'
)]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['category:read', 'transaction:read'])]
    #[OA\Property( description: 'Unique identifier', type: 'string', format: 'uuid' )]
    protected UuidInterface $id;


    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['category:read', 'transaction:read'])]
    #[OA\Property( description: 'Category name', type: 'string', example: 'Groceries' )]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 7, options: ['comment' => 'Hex color code for the category'])]
    #[Groups(['category:read', 'transaction:read'])]
    #[OA\Property(type: 'string', example: '#FF5733')]
    private ?string $color = null;

    #[ORM\Column( length: 20, enumType: TransactionType::class)]
    #[Groups(['category:read'])]
    #[OA\Property( description: 'Transaction type', type: 'string', enum: [ 'INCOME', 'EXPENSE'] )]
    private ?TransactionType $type = null; // INCOME or EXPENSE

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\OneToMany( targetEntity: Transaction::class, mappedBy: 'category' )]
    private Collection $transactions;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['category:read'])]
    #[OA\Property(type: 'string', format: 'date-time')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['category:read'])]
    #[OA\Property(type: 'string', format: 'date-time')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): UuidInterface {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getType(): ?TransactionType
    {
        return $this->type;
    }

    public function setType(TransactionType $type): void
    {
        $this->type = $type;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
