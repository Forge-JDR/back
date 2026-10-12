<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CharacterSheet
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    public function getId(): ?int { return $this->id; }

    #[ORM\ManyToOne(targetEntity: Campaign::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Campaign $campaign;
    public function getCampaign(): Campaign { return $this->campaign; }
    public function setCampaign(Campaign $value): void { $this->campaign = $value; }

    #[ORM\Column(length: 255)]
    private string $name = '';
    public function getName(): string { return $this->name; }
    public function setName(string $value): void { $this->name = $value; }

    #[ORM\Column(type: 'text')]
    private string $notes = '';
    public function getNotes(): string { return $this->notes; }
    public function setNotes(string $value): void { $this->notes = $value; }

    #[ORM\Column(type: 'json')]
    private array $template = [];
    public function getTemplate(): array { return $this->template; }
    public function setTemplate(array $value): void { $this->template = $value; }

    #[ORM\Column(name: 'field_values', type: 'json')]
    private array $values = [];
    public function getValues(): array { return $this->values; }
    public function setValues(array $value): void { $this->values = $value; }

    #[ORM\Column()]
    private int $templateVersion = 1;
    public function getTemplateVersion(): int { return $this->templateVersion; }
    public function setTemplateVersion(int $value): void { $this->templateVersion = $value; }

}
