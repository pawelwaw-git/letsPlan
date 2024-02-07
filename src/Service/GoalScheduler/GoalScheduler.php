<?php

declare(strict_types=1);

namespace App\Service\GoalScheduler;

use App\Entity\Goal;
use App\Entity\TaskCalendar;
use App\Repeatable\RepeatableFactory;
use App\Repository\GoalRepository;
use App\Repository\TaskCalendarRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class GoalScheduler
{
    public const SCHEDULE_ACTION = 'schedule';
    public const QUERY_PARAMS = 'goal_scheduler_param';
    public const SCHEDULE_DATEINTERVAL_TEXT = 'P2M';

    private GoalRepository $goalRepository;
    private TaskCalendarRepository $taskCalendarRepository;
    private RequestStack $request;
    private bool $isScheduleAllowed = false;
    private array $goalsToSchedule;
    private $repeatableType;

    public function __construct(GoalRepository $goalRepository, TaskCalendarRepository $taskCalendarRepository, RequestStack $request)
    {
        $this->goalRepository = $goalRepository;
        $this->taskCalendarRepository = $taskCalendarRepository;
        $this->request = $request;
    }

    public function scheduleGoals()
    {
        if ($this->isScheduleGoalsAllowed()) {
            $this->getGoalsToSchedule();
            foreach ($this->goalsToSchedule as $goal) {
                $this->setRepeatableType($goal);
                if ($this->isRepeatable()) {
                    $this->createTasksBasedOnPeriod($goal);
                }
                $this->saveData();
            }
        }
        $this->resetPermission();
    }

    public function isScheduleGoalsAllowed(): bool
    {
        $this->checkPermissionBasedOnReqestQuery();

        return $this->isScheduleAllowed;
    }

    public function setPermissionToSchedule($allowed = false)
    {
        $this->isScheduleAllowed = $allowed;
    }

    private function setRepeatableType(Goal $goal)
    {
        $this->repeatableType = RepeatableFactory::getSuitableRepeatableType($goal->getRepeatable());
    }

    private function checkPermissionBasedOnReqestQuery()
    {
        if ($this->request->getCurrentRequest()) {
            $inputParams = $this->request->getCurrentRequest()->query->get(GoalScheduler::QUERY_PARAMS);
            if (GoalScheduler::SCHEDULE_ACTION == $inputParams) {
                $this->isScheduleAllowed = true;
            }
        }
    }

    private function getLastScheduleDate(): \DateTime
    {
        $lastScheduleDate = new \DateTime('today');

        return $lastScheduleDate->add(new \DateInterval(self::SCHEDULE_DATEINTERVAL_TEXT))->setTime(0, 0, 0);
    }

    private function getScheduledPeriod(Goal $goal): \DatePeriod
    {
        $startDate = $this->repeatableType->getStartDate();
        $startDate->setTime(0, 0, 0);
        $finishDate = clone $startDate;
        $finishDate->add(new \DateInterval(self::SCHEDULE_DATEINTERVAL_TEXT));
        $finishDate->setTime(0, 0, 0);
        if (is_null($goal->getLastDateSchedule())) {
            return new \DatePeriod($startDate, $this->repeatableType->getInterval(), $finishDate);
        }

        return new \DatePeriod($goal->getLastDateSchedule(), $this->repeatableType->getInterval(), $finishDate, 1);
    }

    private function isRepeatable(): bool
    {
        return $this->repeatableType->isScheduled();
    }

    private function createTasksBasedOnPeriod(Goal $goal)
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

    private function getGoalsToSchedule()
    {
        $this->goalsToSchedule = $this->goalRepository->findGoalsToSchedule($this->getLastScheduleDate());
    }

    private function resetPermission()
    {
        $this->isScheduleAllowed = false;
    }

    private function saveData()
    {
        $this->goalRepository->flush();
        $this->taskCalendarRepository->flush();
    }
}
