<?php

namespace App\Entity;

use App\Repository\WikiRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: WikiRepository::class)]
class Wiki
{

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->Jobs = new ArrayCollection();
        $this->bestiaries = new ArrayCollection();
        $this->Races = new ArrayCollection();
    }



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['wiki.index', 'user.details',  'wiki.index'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['wiki.index', 'user.details', 'wiki.index'])]
    private ?string $Name = null;

    #[ORM\Column(length: 2048)]
    #[Groups(['wiki.details'])]
    private ?string $Content = null;

    #[ORM\Column]
    #[Groups(['wiki.details'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 20, options: ['default' => 'inProgress'])]
    #[Groups(['wiki.details', 'user.details', 'wiki.index'])]
    private ?string $Status = 'inProgress';

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'wikis')]
    #[Groups(['wiki.details', 'wiki.index'])]
    private User $user;

    /**
     * @var Collection<int, Job>
     */
    #[ORM\OneToMany(targetEntity: Job::class, mappedBy: 'wiki', orphanRemoval: true)]
    #[MaxDepth(2)]
    #[Groups(['wiki.details'])]
    private Collection $Jobs;

    /**
     * @var Collection<int, Bestiary>
     */
    #[ORM\OneToMany(targetEntity: Bestiary::class, mappedBy: 'wiki', orphanRemoval: true)]
    #[MaxDepth(2)]
    #[Groups(['wiki.details'])]
    private Collection $bestiaries;

    /**
     * @var Collection<int, Race>
     */
    #[ORM\OneToMany(targetEntity: Race::class, mappedBy: 'wiki', orphanRemoval: true)]
    #[MaxDepth(2)]
    #[Groups(['wiki.details'])]
    private Collection $Races;


    

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
     * @return Collection<int, Job>
     */
    public function getJobs(): Collection
    {
        return $this->Jobs;
    }

    public function addJob(Job $job): static
    {
        if (!$this->Jobs->contains($job)) {
            $this->Jobs->add($job);
            $job->setWiki($this);
        }

        return $this;
    }

    public function removeJob(Job $job): static
    {
        if ($this->Jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            // if ($job->getWiki() === $this) {
            //     $job->setWiki(null);
            // }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bestiary>
     */
    public function getBestiaries(): Collection
    {
        return $this->bestiaries;
    }

    public function addBestiary(Bestiary $bestiary): static
    {
        if (!$this->bestiaries->contains($bestiary)) {
            $this->bestiaries->add($bestiary);
            $bestiary->setWiki($this);
        }

        return $this;
    }

    public function removeBestiary(Bestiary $bestiary): static
    {
        if ($this->bestiaries->removeElement($bestiary)) {
            // set the owning side to null (unless already changed)
            // if ($bestiary->getWiki() === $this) {
            //     $bestiary->setWiki(null);
            // }
        }

        return $this;
    }

    /**
     * @return Collection<int, Race>
     */
    public function getRaces(): Collection
    {
        return $this->Races;
    }

    public function addRace(Race $race): static
    {
        if (!$this->Races->contains($race)) {
            $this->Races->add($race);
            $race->setWiki($this);
        }

        return $this;
    }

    public function removeRace(Race $race): static
    {
        if ($this->Races->removeElement($race)) {
            //set the owning side to null (unless already changed)
            if ($race->getWiki() === $this) {
                $race->setWiki(null);
            }
        }

        return $this;
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
}
