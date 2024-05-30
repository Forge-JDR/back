<?php

namespace App\Entity;

use App\Repository\ScenariRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScenariRepository::class)]
class Scenari
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $narrativeTram = null;

    #[ORM\ManyToOne(inversedBy: 'scenari')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wiki $wiki = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getNarrativeTram(): ?string
    {
        return $this->narrativeTram;
    }

    public function setNarrativeTram(?string $narrativeTram): static
    {
        $this->narrativeTram = $narrativeTram;

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
