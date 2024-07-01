<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BestiaryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: BestiaryRepository::class)]
class Bestiary
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['wiki.details'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['wiki.details'])]
    private ?string $Name = null;

    #[ORM\Column(length: 2048)]
    #[Groups(['wiki.details'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['wiki.details'])]
    private ?string $Type = null;

    #[ORM\Column(length: 512, nullable: true)]
    #[Groups(['wiki.details'])]
    private ?string $imageUrl = null;

    #[ORM\Column]
    #[Groups(['wiki.details'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'bestiaries')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(2)]
    private ?Wiki $wiki = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(?string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): static
    {
        $this->Type = $Type;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createAt): static
    {
        $this->createdAt = $createAt;

        return $this;
    }

    public function getWiki(): ?Wiki
    {
        return $this->wiki;
    }

    public function setWiki(?Wiki $wiki): static
    {
        $this->wiki = $wiki;

        return $this;
    }

}
