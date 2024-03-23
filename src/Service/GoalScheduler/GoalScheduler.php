<?php

declare(strict_types=1);

namespace App\Service\GoalScheduler;

use App\Entity\Goal;
use App\Entity\TaskCalendar;
use App\Repeatable\RepeatableTypeException;
use App\Repository\GoalRepository;
use App\Repository\TaskCalendarRepository;
use Carbon\Carbon;
use Psr\Log\LoggerInterface;

class GoalScheduler
{
    public const SCHEDULE_DATE_INTERVAL_TEXT = 'P2M';

    private GoalRepository $goalRepository;
    private TaskCalendarRepository $taskCalendarRepository;
    private LoggerInterface $logger;

    public function __construct(
        GoalRepository $goalRepository,
        TaskCalendarRepository $taskCalendarRepository,
        LoggerInterface $logger
    ) {
        $this->goalRepository = $goalRepository;
        $this->taskCalendarRepository = $taskCalendarRepository;
        $this->logger = $logger;
    }

    public function scheduleGoals(): void
    {
        $goalsToSchedule = $this->getGoalsToSchedule();
        foreach ($goalsToSchedule as $goal) {
            if ($goal->isPossibleToPlan()) {
                try {
                    $this->createTasksBasedOnPeriod($goal);
                } catch (RepeatableTypeException $e) {
                    $this->logger->error($e->getMessage(), $e->getTrace());
                }
            }
            $this->saveData();
        }
    }

    private function getLastScheduleDate(): \DateTime
    {
        $lastScheduleDate = Carbon::now();

        return $lastScheduleDate->add(new \DateInterval(self::SCHEDULE_DATE_INTERVAL_TEXT))->setTime(0, 0);
    }

    /**
     * @throws RepeatableTypeException
     */
    private function getScheduledPeriod(Goal $goal): \DatePeriod
    {
        $repeatableType = $goal->getRepeatableType();

        $startDate = $repeatableType->getStartDate();
        $startDate->setTime(0, 0);
        $finishDate = clone $startDate;
        $finishDate->add(new \DateInterval(self::SCHEDULE_DATE_INTERVAL_TEXT));
        $finishDate->setTime(0, 0);
        if (is_null($goal->getLastDateSchedule())) {
            return new \DatePeriod($startDate, $repeatableType->getInterval(), $finishDate);
        }

        return new \DatePeriod($goal->getLastDateSchedule(), $repeatableType->getInterval(), $finishDate, 1);
    }

    /**
     * @throws RepeatableTypeException
     */
    private function createTasksBasedOnPeriod(Goal $goal): void
    {
        $scheduledPeriod = $this->getScheduledPeriod($goal);
        foreach ($scheduledPeriod as $date) {
            $task = new TaskCalendar($date, false, $goal);
            $this->taskCalendarRepository->save($task);
        }

        $goal->setLastDateSchedule($scheduledPeriod->getEndDate());
    }

    /**
     * @return Goal[]
     */
    private function getGoalsToSchedule(): array
    {
        return $this->goalRepository->findGoalsToSchedule($this->getLastScheduleDate());
    }

    private function saveData(): void
    {
        $this->goalRepository->flush();
        $this->taskCalendarRepository->flush();
    }
}
