<?php

namespace App\Entity;

use App\Repository\TaskCalendarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskCalendarRepository::class)]
class TaskCalendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $Date = null;

    #[ORM\ManyToOne(targetEntity: Goal::class, inversedBy: 'tasksCalendar')]
    #[ORM\JoinColumn(name: 'goal_id', referencedColumnName: 'id')]
    private ?Goal $Goal = null;

    #[ORM\Column]
    private ?bool $isDone = false;

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
