<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users', options: ['comment' => 'Main user authentication and profile data'])]
#[ORM\HasLifecycleCallbacks]
#[OA\Schema(schema: 'User',title: 'User', description: 'User profile and authentication data.')]
#[ORM\Index(name: 'idx_users_email', columns: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface {

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true, options: ['comment' => 'Primary key, UUID v4'])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['user:read', 'transaction:read', 'category:read', 'account:read'])]
    #[OA\Property(type: 'string', format: 'uuid')]
    protected UuidInterface $id;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'User first name'])]
    #[Groups(['user:read'])]
    #[OA\Property(type: 'string')]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'User last name'])]
    #[Groups(['user:read'])]
    #[OA\Property(type: 'string')]
    private ?string $lastName = null;

    #[ORM\Column(length: 180, unique: true, options: ['comment' => 'Unique email address, used for login'])]
    #[Groups(['user:read'])]
    #[OA\Property(type: 'string', format: 'email')]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $password = null;

    #[ORM\OneToMany( targetEntity: Transaction::class, mappedBy: 'users', orphanRemoval: true )]
    private Collection $transactions;

    #[ORM\OneToMany( targetEntity: Category::class, mappedBy: 'users', orphanRemoval: true )]
    private Collection $categories;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => 'User settings (currency, locale, theme)'])]
    #[Groups(['user:read'])]
    #[OA\Property(type: 'object', example: ['currency' => 'USD'])]
    private ?array $settings = [
        'currency' => 'USD',
        'locale' => 'en',
    ];

    #[ORM\OneToMany( targetEntity: Account::class, mappedBy: 'users', orphanRemoval: true )]
    private Collection $accounts;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => 'Timestamp when the user was created'])]
    #[Groups(['user:read'])]
    #[OA\Property(type: 'string', format: 'date-time')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => 'Timestamp of the last update'])]
    #[Groups(['user:read'])]
    #[OA\Property(type: 'string', format: 'date-time')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->transactions = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    #[\Deprecated(since: 'symfony/security-core 7.3')]
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function setSettings(?array $settings): static
    {
        $this->settings = $settings;

        return $this;
    }

}
