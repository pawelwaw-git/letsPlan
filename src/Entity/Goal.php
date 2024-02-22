<?php

declare(strict_types=1);

namespace App\Entity;

use App\Contracts\Repeatable;
use App\Enum\RepeatableTypes;
use App\Repeatable\RepeatableFactory;
use App\Repeatable\RepeatableTypeException;
use App\Repository\GoalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GoalRepository::class)]
class Goal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $Name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Description = null;

    #[ORM\Column(nullable: true)]
    private ?int $Priority = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Type = null;

    #[ORM\ManyToOne(inversedBy: 'goals')]
    private ?Category $Category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Repeatable = null;

    #[ORM\Column]
    private ?bool $Active = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $LastDateSchedule = null;

    public function __toString()
    {
        return $this->Name.' #'.$this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(?string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->Priority;
    }

    public function setPriority(?int $Priority): self
    {
        $this->Priority = $Priority;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(?string $Type): self
    {
        $this->Type = $Type;

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

    public function getRepeatable(): ?string
    {
        return $this->Repeatable;
    }

    public function setRepeatable(?string $Repeatable): self
    {
        $this->Repeatable = $Repeatable;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->Active;
    }

    public function setActive(bool $isActive): self
    {
        $this->Active = $isActive;

        return $this;
    }

    public function getLastDateSchedule(): ?\DateTimeInterface
    {
        return $this->LastDateSchedule;
    }

    public function setLastDateSchedule(?\DateTimeInterface $LastDateSchedule): self
    {
        $this->LastDateSchedule = $LastDateSchedule;

        return $this;
    }

    /**
     * @throws RepeatableTypeException
     */
    public function getRepeatableType(): Repeatable
    {
        return RepeatableFactory::getSuitableRepeatableType($this->Repeatable);
    }

    public function isPossibleToPlan(): bool
    {
        return match ($this->Repeatable) {
            RepeatableTypes::EveryDay->value, RepeatableTypes::EveryWeek->value, RepeatableTypes::EveryMonth->value => true,
            default => false,
        };
    }
}
