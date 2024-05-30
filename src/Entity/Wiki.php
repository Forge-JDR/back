<?php

namespace App\Entity;

use App\Repository\WikiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WikiRepository::class)]
class Wiki
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 512)]
    private ?string $Description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 20)]
    private ?string $Status = null;

    /**
     * @var Collection<int, Scenari>
     */
    #[ORM\OneToMany(targetEntity: Scenari::class, mappedBy: 'wiki')]
    private Collection $scenari;

    public function __construct()
    {
        $this->scenari = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

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

    public function getStatus(): ?string
    {
        return $this->Status;
    }

    public function setStatus(string $Status): static
    {
        $this->Status = $Status;

        return $this;
    }

    /**
     * @return Collection<int, Scenari>
     */
    public function getScenari(): Collection
    {
        return $this->scenari;
    }

    public function addScenari(Scenari $scenari): static
    {
        if (!$this->scenari->contains($scenari)) {
            $this->scenari->add($scenari);
            $scenari->setWiki($this);
        }

        return $this;
    }

    public function removeScenari(Scenari $scenari): static
    {
        if ($this->scenari->removeElement($scenari)) {
            // set the owning side to null (unless already changed)
            if ($scenari->getWiki() === $this) {
                $scenari->setWiki(null);
            }
        }

        return $this;
    }
}
