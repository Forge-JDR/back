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
#[ORM\UniqueConstraint(name: 'USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
    #[Groups(['user.details', 'wiki.details', 'wiki.index'])]
    private ?string $pseudo = null;

    #[ORM\Column]
    #[Groups(['user.details'])]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups(['user.details'])]
    private ?string $username = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['user.details'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(targetEntity: Wiki::class, mappedBy: 'user', orphanRemoval: true)]
    #[Groups(['user.details'])]
    private Collection $Wikis;

    #[ORM\OneToMany(targetEntity: Caracter::class, mappedBy: 'user', orphanRemoval: true)]
    #[Groups(['user.details'])]
    private Collection $Caracters;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->uuid = uniqid('user_', true);
        $this->status = 'active';
        $this->Wikis = new ArrayCollection();
        $this->Caracters = new ArrayCollection();
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

    /**
     * @return Collection<int, Caracter>
     */
    public function getCaracters(): Collection
    {
        return $this->Caracters;
    }

    /**
     * @param Collection<int, Caracter> $caracters
     */

    public function setCaracters(Collection $caracters): static
    {
        $this->Caracters = $caracters;

        return $this;
    }

    public function addCaracter(Caracter $caracter): static
    {
        dd($this);
        if (!$this->Caracters->contains($caracter)) {
            $this->Caracters[] = $caracter;
            
            $caracter->setUser($this);
        }

        return $this;
    }

    // Return email because lexik_jwt_authentication requires a getUsername method and i can't change it to email 
    public function getUsername(): ?string
    {
        return $this->username;
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

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

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
        return (string) $this->username;
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

}
