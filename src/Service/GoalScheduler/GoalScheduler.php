<?php

namespace App\Service\GoalScheduler;

use App\Entity\TaskCalendar;
use App\Entity\Goal;
use App\Repository\GoalRepository;
use App\Repository\TaskCalendarRepository;
use DateInterval;
use DateTime;
use App\Contracts\Repeatable;
use App\Repeatable\RepeatableFactory;
use Symfony\Component\HttpFoundation\RequestStack;

class GoalScheduler
{
    const SCHEDULE_ACTION = 'schedule';
    const QUERY_PARAMS = 'goal_scheduler_param';
    const SCHEDULE_DATEINTERVAL_TEXT = 'P2M';

    private GoalRepository $goalRepository;
    private TaskCalendarRepository $taskCalendarRepository;
    private RequestStack $request;
    private bool $isScheduleAllowed = false;
    private array $goalsToSchedule;
    // private DatePeriod $scheduledPeriod;
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

    private function setRepeatableType(Goal $goal)
    {
        $this->repeatableType = RepeatableFactory::getSuitableRepeatableType($goal->getRepeatable());
    }

    public function isScheduleGoalsAllowed(): bool
    {
        $this->checkPermissionBasedOnReqestQuery();
        return $this->isScheduleAllowed;
    }

    private function checkPermissionBasedOnReqestQuery()
    {
        if ($this->request->getCurrentRequest()) {
            $inputParams = $this->request->getCurrentRequest()->query->get(GoalScheduler::QUERY_PARAMS);
            if ($inputParams == GoalScheduler::SCHEDULE_ACTION)
                $this->isScheduleAllowed = true;
        }
    }

    public function setPermissionToSchedule($allowed = false)
    {
        $this->isScheduleAllowed = $allowed;
    }

    private function getLastScheduleDate(): DateTime
    {
        $lastScheduleDate = new DateTime('today');
        return $lastScheduleDate->add(new DateInterval(self::SCHEDULE_DATEINTERVAL_TEXT))->setTime(0, 0, 0);
    }

    private function getScheduledPeriod(Goal $goal): \DatePeriod
    {
        $startDate = $this->repeatableType->getStartDate();
        $startDate->setTime(0, 0, 0);
        $finishDate = clone $startDate;
        $finishDate->add(new DateInterval(self::SCHEDULE_DATEINTERVAL_TEXT));
        $finishDate->setTime(0, 0, 0);
        if ($startDate <= $goal->getLastDateSchedule()) {
            return new \DatePeriod($goal->getLastDateSchedule(), $this->repeatableType->getInterval(), $finishDate, 1);
        }
        return new \DatePeriod($startDate, $this->repeatableType->getInterval(), $finishDate);
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
            $task->isIsDone(false);
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
