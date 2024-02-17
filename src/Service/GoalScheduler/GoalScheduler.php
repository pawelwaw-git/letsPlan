<?php

declare(strict_types=1);

namespace App\Service\GoalScheduler;

use App\Entity\Goal;
use App\Entity\TaskCalendar;
use App\Repository\GoalRepository;
use App\Repository\TaskCalendarRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class GoalScheduler
{
    public const SCHEDULE_ACTION = 'schedule';
    public const QUERY_PARAMS = 'goal_scheduler_param';
    public const SCHEDULE_DATE_INTERVAL_TEXT = 'P2M';

    private GoalRepository $goalRepository;
    private TaskCalendarRepository $taskCalendarRepository;
    private RequestStack $request;
    private bool $isScheduleAllowed = false;

    /**
     * @var array<Goal>
     */
    private array $goalsToSchedule;

    public function __construct(GoalRepository $goalRepository, TaskCalendarRepository $taskCalendarRepository, RequestStack $request)
    {
        $this->goalRepository = $goalRepository;
        $this->taskCalendarRepository = $taskCalendarRepository;
        $this->request = $request;
    }

    public function scheduleGoals(): void
    {
        if ($this->isScheduleGoalsAllowed()) {
            $this->getGoalsToSchedule();
            foreach ($this->goalsToSchedule as $goal) {
                if ($this->isRepeatable($goal)) {
                    $this->createTasksBasedOnPeriod($goal);
                }
                $this->saveData();
            }
        }
        $this->resetPermission();
    }

    public function isScheduleGoalsAllowed(): bool
    {
        $this->checkPermissionBasedOnRequestQuery();

        return $this->isScheduleAllowed;
    }

    public function setPermissionToSchedule(bool $allowed = false): void
    {
        $this->isScheduleAllowed = $allowed;
    }

    private function checkPermissionBasedOnRequestQuery(): void
    {
        if ($this->request->getCurrentRequest()) {
            $inputParams = $this->request->getCurrentRequest()->query->get(self::QUERY_PARAMS);
            if ($inputParams === self::SCHEDULE_ACTION) {
                $this->isScheduleAllowed = true;
            }
        }
    }

    private function getLastScheduleDate(): \DateTime
    {
        $lastScheduleDate = new \DateTime('today');

        return $lastScheduleDate->add(new \DateInterval(self::SCHEDULE_DATE_INTERVAL_TEXT))->setTime(0, 0, 0);
    }

    private function getScheduledPeriod(Goal $goal): \DatePeriod
    {
        $repeatableType = $goal->getRepeatableType();

        $startDate = $repeatableType->getStartDate();
        $startDate->setTime(0, 0, 0);
        $finishDate = clone $startDate;
        $finishDate->add(new \DateInterval(self::SCHEDULE_DATE_INTERVAL_TEXT));
        $finishDate->setTime(0, 0, 0);
        if (is_null($goal->getLastDateSchedule())) {
            return new \DatePeriod($startDate, $repeatableType->getInterval(), $finishDate);
        }

        return new \DatePeriod($goal->getLastDateSchedule(), $repeatableType->getInterval(), $finishDate, 1);
    }

    private function isRepeatable(Goal $goal): bool
    {
        return $goal->getRepeatableType()->isScheduled();
    }

    private function createTasksBasedOnPeriod(Goal $goal): void
    {
        $scheduledPeriod = $this->getScheduledPeriod($goal);
        foreach ($scheduledPeriod as $date) {
            $task = new TaskCalendar();
            $task->setDate($date);
            $task->setIsDone(false);
            $task->setGoal($goal);
            $goal->setLastDateSchedule($date);
            $this->taskCalendarRepository->save($task);
        }
    }

    private function getGoalsToSchedule(): void
    {
        $this->goalsToSchedule = $this->goalRepository->findGoalsToSchedule($this->getLastScheduleDate());
    }

    private function resetPermission(): void
    {
        $this->isScheduleAllowed = false;
    }

    private function saveData(): void
    {
        $this->goalRepository->flush();
        $this->taskCalendarRepository->flush();
    }
}
