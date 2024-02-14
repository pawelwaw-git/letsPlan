<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContestGamesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContestGamesRepository::class)]
class ContestGames
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contest $Contest = null;

    #[ORM\Column(nullable: true)]
    private ?int $Result = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Goal $FirstGoal = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Goal $SecondGoal = null;

    #[ORM\Column(nullable: true)]
    private ?int $Step = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContest(): ?Contest
    {
        return $this->Contest;
    }

    public function setContest(?Contest $Contest): self
    {
        $this->Contest = $Contest;

        return $this;
    }

    public function getResult(): ?int
    {
        return $this->Result;
    }

    public function setResult(?int $Result): self
    {
        $this->Result = $Result;

        return $this;
    }

    public function getFirstGoal(): ?Goal
    {
        return $this->FirstGoal;
    }

    public function setFirstGoal(?Goal $FirstGoal): self
    {
        $this->FirstGoal = $FirstGoal;

        return $this;
    }

    public function getSecondGoal(): ?Goal
    {
        return $this->SecondGoal;
    }

    public function setSecondGoal(?Goal $SecondGoal): self
    {
        $this->SecondGoal = $SecondGoal;

        return $this;
    }

    public function getStep(): ?int
    {
        return $this->Step;
    }

    public function setStep(?int $Step): self
    {
        $this->Step = $Step;

        return $this;
    }
}
