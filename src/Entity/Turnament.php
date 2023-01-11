<?php

namespace App\Entity;

use App\Repository\TurnamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Gedmo\Timestampable\Traits\TimestampableEntity;


#[ORM\Entity(repositoryClass: TurnamentRepository::class)]
class Turnament
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

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

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getMaxRounds(): int
    {
        $count = $this->getPlayers()->count();
        if ($count == 0) return 0;
        if ($count < 3) return 1;
        return $this->Rounds = ($count * ($count - 1)) / 2;
    }
}
