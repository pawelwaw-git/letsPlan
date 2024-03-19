<?php

declare(strict_types=1);

namespace App\Tests\Service\GoalScheduler\ScheduleGoals;

use App\Entity\Goal;
use App\Enum\RepeatableTypes;
use App\Repository\GoalRepository;
use App\Repository\TaskCalendarRepository;
use App\Service\GoalScheduler\GoalScheduler;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class GoalSchedulerTest extends TestCase
{
    public function testScheduleGoalsIsNotPossible(): void
    {
        // GIVEN
        $goal = $this->createGoalForSchedule();

        $this->createDateMock();

        $taskCalendarRepository = $this->createMock(TaskCalendarRepository::class);
        $goal_scheduler = $this->createGoalScheduler($taskCalendarRepository);

        // WHEN
        $goal_scheduler->scheduleGoals();

        // THEN
        $this->assertSame('2022-02-02', $goal->getLastDateSchedule());
    }

    public function testGoalsAreScheduled(): void
    {
        // GIVEN
        $goal = $this->createGoalForSchedule();

        $taskCalendarRepository = $this->createMock(TaskCalendarRepository::class);
        $goal_scheduler = $this->createGoalScheduler($taskCalendarRepository);

        // WHEN
        $goal_scheduler->scheduleGoals();

        // THEN
        $this->assertSame('2022-02-02', $goal->getLastDateSchedule());
    }

    private function createGoalForSchedule(): Goal
    {
        $goal = new Goal();
        $goal->setRepeatable(RepeatableTypes::EveryDay->value);
        $goal->setActive(true);

        return $goal;
    }

    private function createGoalScheduler(TaskCalendarRepository $taskCalendarRepository): GoalScheduler
    {
        $goalRepository = $this->createMock(GoalRepository::class);
        $logger = $this->createMock(LoggerInterface::class);

        return new GoalScheduler($goalRepository, $taskCalendarRepository, $logger);
    }

    private function createDateMock(): void
    {
        Carbon::setTestNow('2022-02-02');
    }
}
