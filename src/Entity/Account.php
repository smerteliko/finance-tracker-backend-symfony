<?php

namespace App\Entity;

use App\Enum\AccountType;
use App\Repository\AccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'account', options: ['comment' => 'Financial accounts/wallets owned by users'])]
#[ORM\HasLifecycleCallbacks]
#[OA\Schema(schema: 'Account',title: 'Account', description: 'A financial account/wallet belonging to the user.')]
class Account
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class:UuidGenerator::class)]
    #[Groups(['account:read', 'transaction:read'])]
    #[OA\Property(type: 'string', format: 'uuid')]
    protected UuidInterface $id;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, options: ['comment' => 'Account name (e.g., Cash, Credit Card, Savings)'])]
    #[Groups(['account:read', 'transaction:read'])]
    #[OA\Property(type: 'string')]
    private ?string $name = null;

    #[ORM\Column(length: 50, enumType: AccountType::class, options: ['comment' => 'Type of the account (CHECKING, SAVINGS, CASH, etc.)'])]
    #[Groups(['account:read'])]
    #[OA\Property(type: 'string', enum: ['CHECKING', 'SAVINGS', 'CASH', 'CREDIT_CARD', 'INVESTMENT'])]
    private ?AccountType $type = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['comment' => 'Current balance of the account (Income - Expense)'])]
    #[Groups(['account:read'])]
    #[OA\Property(type: 'number', format: 'float')]
    private float $balance = 0.00; // Track the current balance

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => 'Currency code (e.g., USD, EUR)'])]
    #[Groups(['account:read'])]
    #[OA\Property(type: 'number', format: 'float')]
    private ?string $currency = null;

    #[ORM\OneToMany( targetEntity: Transaction::class, mappedBy: 'account', orphanRemoval: true )]
    private Collection $transactions;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => 'Timestamp when the account was created'])]
    #[Groups(['account:read'])]
    #[OA\Property(type: 'string', format: 'date-time')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => 'Timestamp of the last update'])]
    #[Groups(['account:read'])]
    #[OA\Property(type: 'string', format: 'date-time')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): void {
        $this->user = $user;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function getType(): ?AccountType {
        return $this->type;
    }

    public function setType(?AccountType $type): void {
        $this->type = $type;
    }

    public function getBalance(): float {
        return $this->balance;
    }

    public function setBalance(float $balance): void {
        $this->balance = $balance;
    }

    public function getCurrency(): ?string {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void {
        $this->currency = $currency;
    }

    public function getTransactions(): Collection {
        return $this->transactions;
    }

    public function setTransactions(Collection $transactions): void {
        $this->transactions = $transactions;
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

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable {
        return $this->updatedAt;
    }

}
