<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Campaign
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    public function getId(): ?int { return $this->id; }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;
    public function getOwner(): User { return $this->owner; }
    public function setOwner(User $value): void { $this->owner = $value; }

    #[ORM\ManyToOne(targetEntity: Wiki::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Wiki $wiki;
    public function getWiki(): Wiki { return $this->wiki; }
    public function setWiki(Wiki $value): void { $this->wiki = $value; }

    #[ORM\Column(length: 255)]
    private string $name = '';
    public function getName(): string { return $this->name; }
    public function setName(string $value): void { $this->name = $value; }

    #[ORM\Column(type: 'text')]
    private string $description = '';
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $value): void { $this->description = $value; }

    #[ORM\Column(type: 'text')]
    private string $notes = '';
    public function getNotes(): string { return $this->notes; }
    public function setNotes(string $value): void { $this->notes = $value; }

    #[ORM\Column(type: 'json')]
    private array $journal = [];
    public function getJournal(): array { return $this->journal; }
    public function setJournal(array $value): void { $this->journal = $value; }

}
