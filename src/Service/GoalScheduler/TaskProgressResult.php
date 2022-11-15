<?php
namespace App\Service\GoalScheduler;

use App\Repository\TaskCalendarRepository;

class TaskProgressResult
{
    private $all_tasks;
    private $finished_tasks;
    private $undone_tasks;

    private TaskCalendarRepository $taskCalendarRepository;

    public function __construct(TaskCalendarRepository $taskCalendarRepository)
    {
        $this->taskCalendarRepository = $taskCalendarRepository;
    }

    private function resetProgressResult()
    {
        $this->all_tasks = [];
        $this->finished_tasks = [];
        $this->undone_tasks = [];
    }

    public function getProgressResult(string $before)
    {
        $this->resetProgressResult();
        $PreviousTasks = $this->taskCalendarRepository->getStatsForPreviosTasks(new \Datetime($before));
        foreach ($PreviousTasks as $statisticTaskRow) {
            $this->initTableIfNotExists($statisticTaskRow);
            $this->incrementValues($statisticTaskRow);
        }
    }

    private function initTableIfNotExists(array $statisticTaskRow)
    {
        if (!isset($this->all_tasks[$statisticTaskRow['Date']->format("Y-m-d")]))
            $this->all_tasks[$statisticTaskRow['Date']->format("Y-m-d")] = 0;
        if (!isset($this->undone_tasks[$statisticTaskRow['Date']->format("Y-m-d")]))
            $this->undone_tasks[$statisticTaskRow['Date']->format("Y-m-d")] = 0;
        if (!isset($this->finished_tasks[$statisticTaskRow['Date']->format("Y-m-d")]))
            $this->finished_tasks[$statisticTaskRow['Date']->format("Y-m-d")] = 0;
    }

    private function incrementValues(array $statisticTaskRow)
    {
        if ($statisticTaskRow['isDone']) {
            $this->finished_tasks[$statisticTaskRow['Date']->format("Y-m-d")] += $statisticTaskRow['Quantity'];
        } else {
            $this->undone_tasks[$statisticTaskRow['Date']->format("Y-m-d")] += $statisticTaskRow['Quantity'];
        }
        $this->all_tasks[$statisticTaskRow['Date']->format("Y-m-d")] += $statisticTaskRow['Quantity'];
    }

    public function getAllTasks()
    {
        return $this->all_tasks;
    }

    public function getUndoneTasks()
    {
        return $this->undone_tasks;
    }

    public function getFinishedTasks()
    {
        return $this->finished_tasks;
    }
}