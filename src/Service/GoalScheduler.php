<?php

namespace App\Service;

use App\Entity\TaskCalendar;
use App\Repository\GoalRepository;
use App\Repository\TaskCalendarRepository;
use DateInterval;
use DateTime;
use App\Contracts\IsScheduled;
use App\Contracts\Repeatable;
use App\Repeatable\RepeatableFactory;
use Symfony\Component\HttpFoundation\RequestStack;

class GoalScheduler
{
    const SCHEDULE_ACTION = 'schedule';
    const QUERY_PARAMS = 'goal_scheduler_param';
    const SCHEDULE_DATEINTERVAL_TEXT = 'P2M';

    private $goalRepository;
    private $taskCalendarRepository;
    private $request;

    public function __construct(GoalRepository $goalRepository, TaskCalendarRepository $taskCalendarRepository, RequestStack $request)
    {
        $this->goalRepository = $goalRepository;
        $this->taskCalendarRepository = $taskCalendarRepository;
        $this->request = $request;
    }

    public function scheduleGoals()
    {
        if ($this->canScheduleGoals()) {
            $goalsToSchedule = $this->goalRepository->findGoalsToSchedule($this->getLastScheduleDate());
            foreach ($goalsToSchedule as $goal) {
                $repeatableType = RepeatableFactory::getSuitableRepeatableType($goal->getRepeatable());
                if ($this->canScheduleGoal($repeatableType)) {
                    $scheduledPeriod = $this->getScheduledPeriod($repeatableType, $goal->getLastDateSchedule());
                    if ($scheduledPeriod) {
                        foreach ($scheduledPeriod as $date) {
                            $task = new TaskCalendar();
                            $task->setDate($date);
                            $task->isIsDone(false);
                            $task->setGoal($goal);
                            $goal->setLastDateSchedule($date);
                            $this->taskCalendarRepository->save($task);
                        }
                    }
                }
                $this->goalRepository->flush();
                $this->taskCalendarRepository->flush();
            }
        }
    }

    private function canScheduleGoals(): bool
    {
        $inputParams = $this->request->getCurrentRequest()->query->get(GoalScheduler::QUERY_PARAMS);
        if ($inputParams == GoalScheduler::SCHEDULE_ACTION)
            return true;
        return false;
    }

    private function getLastScheduleDate(): DateTime
    {
        $lastScheduleDate = new DateTime('today');
        return $lastScheduleDate->add(new DateInterval(self::SCHEDULE_DATEINTERVAL_TEXT))->setTime(0, 0, 0);
    }

    private function getScheduledPeriod(Repeatable $Repeatable, ?DateTime $lastScheduleDate): \DatePeriod
    {
        $startDate = $Repeatable->getStartDate();
        $startDate->setTime(0, 0, 0);
        $finishDate = clone $startDate;
        $finishDate->add(new DateInterval(self::SCHEDULE_DATEINTERVAL_TEXT));
        $finishDate->setTime(0, 0, 0);
        if ($startDate <= $lastScheduleDate) {
            return new \DatePeriod($lastScheduleDate, $Repeatable->getInterval(), $finishDate, 1);
        }
        return new \DatePeriod($startDate, $Repeatable->getInterval(), $finishDate);
    }

    private function canScheduleGoal(IsScheduled $schedule): bool
    {
        return $schedule->isScheduled();
    }
}
