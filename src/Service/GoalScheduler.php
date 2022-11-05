<?php

namespace App\Service;

use App\Entity\TaskCalendar;
use App\Enum\RepeatableTypes;
use App\Repository\GoalRepository;
use App\Repository\TaskCalendarRepository;
use DateInterval;
use DateTime;
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
        $goalsToSchedule = $this->goalRepository->findGoalsToSchedule($this->getLastScheduleDate());
        // dump($goalsToSchedule);

        //  because service is autowired we need to check if we should schedule our Goals,
        //  but this is temporary I should run method and make redirect, but what if someone get route by accident
        // if ($this->canScheduleGoals()) {
        //     $goalsToSchedule = $this->goalRepository->findGoalsToSchedule($this->getLastScheduleDate());
        //     //this should be corelated with Reapeatable type enum
        //     $intervals = [
        //         RepeatableTypes::EveryDay->value => new \DateInterval('P1D'),
        //         RepeatableTypes::EveryWeek->value => new \DateInterval('P1W'),
        //         RepeatableTypes::EveryMonth->value => new \DateInterval('P1M'),
        //     ];
        //     foreach ($intervals as $repeatable => $interval) {
        //         $scheduledPeriod = $this->getScheduledPeriod($interval);
        //         foreach ($goalsToSchedule as $goal) {
        //             if ($goal->getRepeatable() == $repeatable) {
        //                 foreach ($scheduledPeriod as $date) {
        //                     $task = new TaskCalendar();
        //                     $task->setDate($date);
        //                     $task->isIsDone(false);
        //                     $task->setGoal($goal);
        //                     $goal->setLastDateSchedule($date);
        //                     $this->taskCalendarRepository->save($task);
        //                 }
        //             }
        //         }
        //     }
        //     $this->goalRepository->flush();
        //     $this->taskCalendarRepository->flush();
        // }
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
        $lastScheduleDate = new DateTime('last day of previous month');
        return $lastScheduleDate->add(new DateInterval(self::SCHEDULE_DATEINTERVAL_TEXT))->setTime(0,0,0);
    }

    private function getScheduledPeriod(DateInterval $interval)
    {
        $start = $this->getStartPeriodDate($interval);
        $end = clone $start;
        $end->add(new DateInterval(self::SCHEDULE_DATEINTERVAL_TEXT));
        $end->modify('first day of this month');
        return new \DatePeriod($start, $interval, $end);
    }

    private function getStartPeriodDate(DateInterval $interval): DateTime
    {
        $today = new \DateTimeImmutable('today');
        $formattedToday = $today->add($interval)->format("Y-m-d");
        $day = $today->modify('+ 1 day')->format("Y-m-d");
        $week = $today->modify('+ 1 week')->format("Y-m-d");
        $month = $today->modify('+ 1 month')->format("Y-m-d");

        $period = match ($formattedToday) {
            $day => new \DateTime('today'),
            $week => new \DateTime('next week'),
            $month => new \DateTime('first day of this month'),
        };

        return $period->setTime(0, 0, 0);
    }
}
