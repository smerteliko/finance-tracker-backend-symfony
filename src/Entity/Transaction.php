<?php

namespace App\Entity;

use App\Enum\TransactionType;
use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\Table(name: 'transactions', options: ['comment' => 'Financial transactions (income/expense) records'])]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'idx_transactions_user_id', columns: ['user_id'])]
#[ORM\Index(name: 'idx_transactions_category_id', columns: ['category_id'])]
#[ORM\Index(name: 'idx_transactions_date', columns: ['date'])]
#[ORM\Index(name: 'idx_transactions_type', columns: ['type'])]
#[ORM\Index(name: 'idx_transactions_user_type', columns: ['user_id', 'type'])]
#[ORM\Index(name: 'idx_transactions_user_category', columns: ['user_id', 'category_id'])]
#[ORM\Index(name: 'idx_transactions_user_date_type', columns: ['user_id', 'date', 'type'])]
#[OA\Schema(schema: 'Transaction', title: 'Transaction', description: 'A single income or expense record.')]
class Transaction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['transaction:read'])]
    #[OA\Property(type: 'string', format: 'uuid')]
    protected UuidInterface $id;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2,options: ['comment' => 'Transaction amount'])]
    #[Groups(['transaction:read'])]
    #[OA\Property(type: 'number', format: 'float')]
    private ?string $amount = null;

    #[ORM\Column(type: 'string', enumType: TransactionType::class, options: ['comment' => 'Transaction type (INCOME or EXPENSE)'])]
    #[Groups(['transaction:read'])]
    #[OA\Property(type: 'string', enum: ['INCOME', 'EXPENSE'])]
    private ?TransactionType $type = null; // INCOME or EXPENSE

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true, options: ['comment' => 'Brief description or memo'])]
    #[Groups(['transaction:read'])]
    #[OA\Property(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => 'The date and time the transaction occurred'])]
    #[Groups(['transaction:read'])]
    #[OA\Property(type: 'string', format: 'date-time')]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false, options: ['comment' => 'Foreign key to the associated category'])]
    #[Groups(['transaction:read'])]
    #[OA\Property(ref: '#/components/schemas/Category')]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false, options: ['comment' => 'Foreign key to the associated account'])]
    #[Groups(['transaction:read'])]
    #[OA\Property(ref: '#/components/schemas/Account')]
    private ?Account $account = null;
    #[ORM\Column(type: 'text', nullable: true, options: ['comment' => 'Detailed user notes'])]
    #[Groups(['transaction:read'])]
    #[OA\Property(type: 'string', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['transaction:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['transaction:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->date = new \DateTimeImmutable();
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


    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    public function getType(): ?TransactionType
    {
        return $this->type;
    }

    public function setType(TransactionType $type): void
    {
        $this->type = $type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

}
