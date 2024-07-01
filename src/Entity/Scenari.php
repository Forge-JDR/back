<?php

namespace App\Entity;

use App\Repository\ScenariRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ScenariRepository::class)]
class Scenari
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['scenari.index'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['scenari.index'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['scenari.details'])]
    private ?string $narrativeTram = null;

    #[ORM\ManyToOne(inversedBy: 'scenari')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(2)]
    #[Groups(['details'])]
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
