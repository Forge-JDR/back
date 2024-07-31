<?php

namespace App\Entity;

use App\Repository\CaracterRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\WikiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: CaracterRepository::class)]
#[Vich\Uploadable]
class Caracter
{


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['caracter.details', 'caracter.index'])]
    private $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['caracter.details', 'caracter.index'])]
    private ?string $Name = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['caracter.index'])]
    private ?string $Content = null;

    #[ORM\Column]
    #[Groups(['caracter.details'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['caracter.details', 'caracter.index'])]
    private ?Picture $imageFile = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'caracters')]
    #[Groups(['caracter.details', 'caracter.index'])]
    private User $user;

     public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }


    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->Content;
    }

    public function setContent(string $Content): static
    {
        $this->Content = $Content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setImageFile(?Picture $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function getImageFile(): ?Picture
    {
        return $this->imageFile;
    }

    
}
