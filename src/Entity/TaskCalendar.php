<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TaskCalendarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: TaskCalendarRepository::class)]
class TaskCalendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    private ?\DateTimeInterface $Date = null;

    #[ORM\ManyToOne(targetEntity: Goal::class, inversedBy: 'tasksCalendar')]
    #[ORM\JoinColumn(name: 'goal_id', referencedColumnName: 'id')]
    private ?Goal $Goal = null;

    #[ORM\Column]
    private ?bool $isDone = false;

    public function __construct(?\DateTimeInterface $Date, bool $isDone, Goal $Goal)
    {
        $this->Date = $Date;
        $this->isDone = $isDone;
        $this->Goal = $Goal;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getGoal(): ?Goal
    {
        return $this->Goal;
    }

    public function setGoal(?Goal $Goal): self
    {
        $this->Goal = $Goal;

        return $this;
    }

    public function isIsDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): self
    {
        $this->isDone = $isDone;

        return $this;
    }
}
