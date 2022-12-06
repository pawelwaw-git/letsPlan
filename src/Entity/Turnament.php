<?php

namespace App\Entity;

use App\Repository\TurnamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;


#[ORM\Entity(repositoryClass: TurnamentRepository::class)]
class Turnament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\ManyToMany(targetEntity: Goal::class, inversedBy: 'turnaments')]
    private Collection $Players;

    #[ORM\Column]
    private ?int $Rounds = null;

    #[ORM\Column]
    private ?int $CurrentRound = null;

    #[ORM\Column]
    private ?bool $Finished = false;

    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    public function __construct()
    {
        $this->Players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return Collection<int, Goal>
     */
    public function getPlayers(): Collection
    {
        return $this->Players;
    }

    public function addPlayer(Goal $player): self
    {
        if (!$this->Players->contains($player)) {
            $this->Players->add($player);
        }

        return $this;
    }

    public function removePlayer(Goal $player): self
    {
        $this->Players->removeElement($player);

        return $this;
    }

    public function getRounds(): ?int
    {
        return $this->Rounds;
    }

    public function setRounds(int $Rounds): self
    {
        $this->Rounds = $Rounds;

        return $this;
    }

    public function getCurrentRound(): ?int
    {
        return $this->CurrentRound;
    }

    public function setCurrentRound(int $CurrentRound): self
    {
        $this->CurrentRound = $CurrentRound;

        return $this;
    }

    public function isFinished(): ?bool
    {
        return $this->Finished;
    }

    public function setFinished(bool $Finished): self
    {
        $this->Finished = $Finished;

        return $this;
    }
}
