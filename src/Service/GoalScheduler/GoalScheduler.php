<?php

declare(strict_types=1);

namespace App\Service\GoalScheduler;

use App\Entity\Goal;
use App\Entity\TaskCalendar;
use App\Repeatable\RepeatableTypeException;
use App\Repository\GoalRepository;
use App\Repository\TaskCalendarRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class GoalScheduler
{
    public const SCHEDULE_ACTION = 'schedule';
    public const QUERY_PARAMS = 'goal_scheduler_param';
    public const SCHEDULE_DATE_INTERVAL_TEXT = 'P2M';

    private GoalRepository $goalRepository;
    private TaskCalendarRepository $taskCalendarRepository;
    private RequestStack $request;
    private LoggerInterface $logger;

    private bool $isScheduleAllowed = false;

    public function __construct(
        GoalRepository $goalRepository,
        TaskCalendarRepository $taskCalendarRepository,
        RequestStack $request,
        LoggerInterface $logger
    ) {
        $this->goalRepository = $goalRepository;
        $this->taskCalendarRepository = $taskCalendarRepository;
        $this->request = $request;
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
        }

        $this->saveData();
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
            // TODO: Zamiast setterów wykorzystać konstruktor
            $task = new TaskCalendar();
            $task->setDate($date);
            $task->setIsDone(false);
            $task->setGoal($goal);
            $goal->setLastDateSchedule($date);

            $this->taskCalendarRepository->save($task);
        }
    }

    /**
     * @return Goal[]
     */
    private function getGoalsToSchedule(): array
    {
        return $this->goalRepository->findGoalsToSchedule($this->getLastScheduleDate());
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
