<?php

namespace App\Service;

use App\Entity\TaskCalendar;
use App\Enum\RepeatableTypes;
use App\Repository\GoalRepository;
use App\Repository\TaskCalendarRepository;
use DateInterval;
use DateTime;
use App\Enum\RepetableTypeException;
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
                $scheduledPeriod = $this->getScheduledPeriod($goal->getRepeatable(), $goal->getLastDateSchedule());
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

    private function getScheduledPeriod(string $Repeatable, $lastScheduleDate): mixed
    {
        $finishDate = new \DateTime('today');
        $finishDate->setTime(0, 0, 0);

        if (!$lastScheduleDate) {
            $lastScheduleDate = clone $finishDate;
        }

        // is Scheduled - throw Exception or false -- 
        // get start Date
            // if lastScheduleDate is null then setStartDate based on Interval - maybe contract is needed there
            // how to solve problem with unique value of text Repeatable ?? 
            // - unique repeatable guid text + method for make name for user, to choose repeatable
            // default will be name of Class - no method in contract
        // get finish Date
        // then return Period to schedule tasks

        try {
            $interval = RepeatableTypes::getSuitableInterval($Repeatable);
        } catch (RepetableTypeException $e) {
            dump("Value Repeatable don't suits for RepeatableTypes Enum");
            return null;
        }
        if (!$interval) return null;

        $finishDate->add(new DateInterval(self::SCHEDULE_DATEINTERVAL_TEXT));
        return new \DatePeriod($lastScheduleDate, $interval, $finishDate);
    }
}
