<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_UUID', fields: ['uuid'])]
#[ORM\UniqueConstraint(name: 'EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user.index', 'wiki.details', 'wiki.index'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user.index'])]
    private ?string $uuid = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user.details'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups(['user.details'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user.details'])]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups(['user.details', 'wiki.details', 'wiki.index'])]
    private ?string $username = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['user.details'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(targetEntity: Wiki::class, mappedBy: 'user', orphanRemoval: true)]
    #[Groups(['user.details'])]
    private Collection $Wikis;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->uuid = uniqid('user_', true);
        $this->status = 'active';
        $this->Wikis = new ArrayCollection();
    }

    /**
     * @return Collection<int, Wiki>
     */
    public function getWikis(): Collection
    {
        return $this->Wikis;
    }

    /**
     * @param Collection<int, Wiki> $wikis
     */
    public function setWikis(Collection $wikis): static
    {
        $this->Wikis = $wikis;

        return $this;
    }

    public function addWiki(Wiki $wiki): static
    {
        if (!$this->Wikis->contains($wiki)) {
            $this->Wikis[] = $wiki;
            $wiki->setUser($this);
        }

        return $this;
    }

    public function getRealUsername(): ?string
    {
        return $this->username;
    }

    // Return email because lexik_jwt_authentication requires a getUsername method and i can't change it to email 
    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

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


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->uuid;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Assure que chaque utilisateur a ce rôle par défaut
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

   public static function createFromPayload($username, array $payload)
    {
        $user = new self();
        $user->setEmail($payload['email']);
        $user->setUsername($payload['username']);
        $user->setRoles($payload['roles']);
        $user->setUuid($payload['uuid']);
        $user->setUsername($payload['username']);
        $user->setStatus($payload['status']);
        return $user;
    }
}
