<?php

namespace App\Entity;

use App\Repository\ContestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContestRepository::class)]
class Contest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $CurrentStep = null;

    #[ORM\Column(nullable: true)]
    private ?int $MaxSteps = null;

    #[ORM\Column]
    private ?bool $Active = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $Category = null;

    #[ORM\Column(length: 255)]
    private ?string $Type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrentStep(): ?int
    {
        return $this->CurrentStep;
    }

    public function setCurrentStep(?int $CurrentStep): self
    {
        $this->CurrentStep = $CurrentStep;

        return $this;
    }

    public function getMaxSteps(): ?int
    {
        return $this->MaxSteps;
    }

    public function setMaxSteps(?int $MaxSteps): self
    {
        $this->MaxSteps = $MaxSteps;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->Active;
    }

    public function setActive(bool $Active): self
    {
        $this->Active = $Active;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCategory(?Category $Category): self
    {
        $this->Category = $Category;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): self
    {
        $this->Type = $Type;

        return $this;
    }
}
