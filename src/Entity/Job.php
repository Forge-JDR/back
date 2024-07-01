<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\JobRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ApiResource]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['wiki.details'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['wiki.details'])]
    private ?string $name = null;

    #[ORM\Column(length: 2048)]
    #[Groups(['wiki.details'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['wiki.details'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'Jobs')]
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
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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
