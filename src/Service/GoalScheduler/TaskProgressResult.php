<?php
namespace App\Service\GoalScheduler;

use App\Repository\TaskCalendarRepository;

class TaskProgressResult
{
    /**
     * @var array<string, int> $allTasks
     */
    private array $allTasks;
    /**
     * @var array<string, int> $finishedTasks
     */
    private array $finishedTasks;
    /**
     * @var array<string, int> $undoneTasks
     */
    private array $undoneTasks;

    private TaskCalendarRepository $taskCalendarRepository;

    public function __construct(TaskCalendarRepository $taskCalendarRepository)
    {
        $this->taskCalendarRepository = $taskCalendarRepository;
    }

    private function resetProgressResult(): void
    {
        $this->allTasks = [];
        $this->finishedTasks = [];
        $this->undoneTasks = [];
    }

    public function getProgressResult(string $before): void
    {
        $this->resetProgressResult();
        $PreviousTasks = $this->taskCalendarRepository->getStatsForPreviousTasks(new \Datetime($before));
        foreach ($PreviousTasks as $statisticTaskRow) {
            /** @var array<int, array<int,string,bool> $statisticTaskRow */
            $this->initTableIfNotExists($statisticTaskRow);
            $this->incrementValues($statisticTaskRow);
        }
    }

    /**
     * @param array<string, mixed> $statisticTaskRow
     */
    private function initTableIfNotExists(array $statisticTaskRow): void
    {
        if (!isset($this->allTasks[$statisticTaskRow['Date']->format("Y-m-d")])) {
            $this->allTasks[$statisticTaskRow['Date']->format("Y-m-d")] = 0;
        }
        if (!isset($this->undoneTasks[$statisticTaskRow['Date']->format("Y-m-d")])) {
            $this->undoneTasks[$statisticTaskRow['Date']->format("Y-m-d")] = 0;
        }
        if (!isset($this->finishedTasks[$statisticTaskRow['Date']->format("Y-m-d")])) {
            $this->finishedTasks[$statisticTaskRow['Date']->format("Y-m-d")] = 0;
        }
    }

    /**
     * @param array<string, mixed> $statisticTaskRow
     */
    private function incrementValues(array $statisticTaskRow): void
    {
        if ($statisticTaskRow['isDone']) {
            $this->finishedTasks[$statisticTaskRow['Date']->format("Y-m-d")] += $statisticTaskRow['Quantity'];
        } else {
            $this->undoneTasks[$statisticTaskRow['Date']->format("Y-m-d")] += $statisticTaskRow['Quantity'];
        }
        $this->allTasks[$statisticTaskRow['Date']->format("Y-m-d")] += $statisticTaskRow['Quantity'];
    }

    /**
     * @return  array<string, int> $allTasks
     */
    public function getAllTasks(): array
    {
        return $this->allTasks;
    }

    /**
     * @return  array<string, int> $undoneTasks
     */
    public function getUndoneTasks(): array
    {
        return $this->undoneTasks;
    }

    /**
     * @return  array<string, int> $finishedTasks
     */
    public function getFinishedTasks(): array
    {
        return $this->finishedTasks;
    }
}