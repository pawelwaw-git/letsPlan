<?php

declare(strict_types=1);

namespace App\Service\GoalScheduler;

use App\Repository\TaskCalendarRepository;

class TaskProgressResult
{
    private $allTasks;
    private $finishedTasks;
    private $undoneTasks;

    private TaskCalendarRepository $taskCalendarRepository;

    public function __construct(TaskCalendarRepository $taskCalendarRepository)
    {
        $this->taskCalendarRepository = $taskCalendarRepository;
    }

    public function getProgressResult(string $before)
    {
        $this->resetProgressResult();
        $PreviousTasks = $this->taskCalendarRepository->getStatsForPreviosTasks(new \DateTime($before));
        foreach ($PreviousTasks as $statisticTaskRow) {
            $this->initTableIfNotExists($statisticTaskRow);
            $this->incrementValues($statisticTaskRow);
        }
    }

    public function getAllTasks()
    {
        return $this->allTasks;
    }

    public function getUndoneTasks()
    {
        return $this->undoneTasks;
    }

    public function getFinishedTasks()
    {
        return $this->finishedTasks;
    }

    private function resetProgressResult()
    {
        $this->allTasks = [];
        $this->finishedTasks = [];
        $this->undoneTasks = [];
    }

    private function initTableIfNotExists(array $statisticTaskRow)
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

    private function incrementValues(array $statisticTaskRow)
    {
        if ($statisticTaskRow['isDone']) {
            $this->finishedTasks[$statisticTaskRow['Date']->format('Y-m-d')] += $statisticTaskRow['Quantity'];
        } else {
            $this->undoneTasks[$statisticTaskRow['Date']->format('Y-m-d')] += $statisticTaskRow['Quantity'];
        }
        $this->allTasks[$statisticTaskRow['Date']->format('Y-m-d')] += $statisticTaskRow['Quantity'];
    }
}
