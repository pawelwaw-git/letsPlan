<?php

declare(strict_types=1);

namespace App\Service\GoalScheduler;

class TaskChartDatasetGenerator
{
    private TaskProgressResult $taskProgressResult;

    public function __construct(TaskProgressResult $taskProgressResult)
    {
        $this->taskProgressResult = $taskProgressResult;
    }

    /**
     * @return array<int,array<string,string>>
     */
    public function getChartDatasetDaysBefore(string $before): array
    {
        $this->taskProgressResult->getProgressResult($before);

        return [
            [
                'type' => 'bar',
                'label' => 'Task in this period',
                'backgroundColor' => 'rgb(0, 0, 132,0.4)',
                'borderColor' => 'rgb(0, 0, 255)',
                'data' => $this->taskProgressResult->getAllTasks(),
            ],
            [
                'type' => 'bar',
                'label' => 'Done Task!',
                'backgroundColor' => 'rgb(0, 132, 0)',
                'borderColor' => 'rgb(0, 0, 255)',
                'tension' => '0.4',
                'data' => $this->taskProgressResult->getFinishedTasks(),
            ],
            [
                'type' => 'bar',
                'label' => 'Undone Task',
                'backgroundColor' => 'rgb(255, 0, 0)',
                'borderColor' => 'rgb(255, 0, 0)',
                'tension' => '0.2',
                'data' => $this->taskProgressResult->getUndoneTasks(),
            ],
        ];
    }
}
