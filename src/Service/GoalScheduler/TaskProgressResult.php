<?php

declare(strict_types=1);

namespace App\Service\GoalScheduler;

use App\Repository\TaskCalendarRepository;

class TaskProgressResult
{
    /**
     * @var array<string, int>
     */
    private array $allTasks;

    /**
     * @var array<string, int>
     */
    private array $finishedTasks;

    /**
     * @var array<string, int>
     */
    private array $undoneTasks;

    private TaskCalendarRepository $taskCalendarRepository;

    public function __construct(TaskCalendarRepository $taskCalendarRepository)
    {
        $this->taskCalendarRepository = $taskCalendarRepository;
    }

    public function getProgressResult(string $before): void
    {
        $this->resetProgressResult();
        $PreviousTasks = $this->taskCalendarRepository->getStatsForPreviousTasks(new \DateTime($before));
        foreach ($PreviousTasks as $statisticTaskRow) {
            // @var array<int, array<int,string,bool> $statisticTaskRow
            $this->initTableIfNotExists($statisticTaskRow);
            $this->incrementValues($statisticTaskRow);
        }
    }

    /**
     * @return array<string, int> $allTasks
     */
    public function getAllTasks(): array
    {
        return $this->allTasks;
    }

    /**
     * @return array<string, int> $undoneTasks
     */
    public function getUndoneTasks(): array
    {
        return $this->undoneTasks;
    }

    /**
     * @return array<string, int> $finishedTasks
     */
    public function getFinishedTasks(): array
    {
        return $this->finishedTasks;
    }

    private function resetProgressResult(): void
    {
        $this->allTasks = [];
        $this->finishedTasks = [];
        $this->undoneTasks = [];
    }

    /**
     * @param array<string, mixed> $statisticTaskRow
     */
    private function initTableIfNotExists(array $statisticTaskRow): void
    {
        if (!isset($this->allTasks[$statisticTaskRow['Date']->format('Y-m-d')])) {
            $this->allTasks[$statisticTaskRow['Date']->format('Y-m-d')] = 0;
        }
        if (!isset($this->undoneTasks[$statisticTaskRow['Date']->format('Y-m-d')])) {
            $this->undoneTasks[$statisticTaskRow['Date']->format('Y-m-d')] = 0;
        }
        if (!isset($this->finishedTasks[$statisticTaskRow['Date']->format('Y-m-d')])) {
            $this->finishedTasks[$statisticTaskRow['Date']->format('Y-m-d')] = 0;
        }
    }

    /**
     * @param array<string, mixed> $statisticTaskRow
     */
    private function incrementValues(array $statisticTaskRow): void
    {
        if ($statisticTaskRow['isDone']) {
            $this->finishedTasks[$statisticTaskRow['Date']->format('Y-m-d')] += $statisticTaskRow['Quantity'];
        } else {
            $this->undoneTasks[$statisticTaskRow['Date']->format('Y-m-d')] += $statisticTaskRow['Quantity'];
        }
        $this->allTasks[$statisticTaskRow['Date']->format('Y-m-d')] += $statisticTaskRow['Quantity'];
    }
}
