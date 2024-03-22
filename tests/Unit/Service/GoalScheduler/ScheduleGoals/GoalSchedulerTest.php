<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\GoalScheduler\ScheduleGoals;

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
        $this->createDateMock();
        $goal = $this->createInactiveGoal();

        $goalRepository = $this->createMock(GoalRepository::class);
        $goalRepository->method('findGoalsToSchedule')->willReturn([$goal]);
        $goal_scheduler = $this->createGoalScheduler($goalRepository);

        // WHEN
        $goal_scheduler->scheduleGoals();

        // THEN
        $this->assertNull($goal->getLastDateSchedule());
    }

    public function testGoalsAreScheduled(): void
    {
        // GIVEN
        $this->createDateMock();
        $goal = $this->createActiveGoal();

        $goalRepository = $this->createMock(GoalRepository::class);
        $goalRepository->method('findGoalsToSchedule')->willReturn([$goal]);
        $goal_scheduler = $this->createGoalScheduler($goalRepository);

        // WHEN
        $goal_scheduler->scheduleGoals();

        // THEN
        $this->assertSame('2024-02-02', $goal->getLastDateSchedule()->format('Y-m-d'));
    }

    private function createActiveGoal(): Goal
    {
        $goal = new Goal();
        $goal->setRepeatable(RepeatableTypes::EveryDay->value);
        $goal->setActive(true);

        return $goal;
    }

    private function createGoalScheduler(GoalRepository $goalRepository): GoalScheduler
    {
        $taskCalendarRepository = $this->createMock(TaskCalendarRepository::class);
        $logger = $this->createMock(LoggerInterface::class);

        return new GoalScheduler($goalRepository, $taskCalendarRepository, $logger);
    }

    private function createDateMock(): void
    {
        Carbon::setTestNow('2023-12-02');
    }

    private function createInactiveGoal(): Goal
    {
        $goal = new Goal();
        $goal->setRepeatable(RepeatableTypes::EveryDay->value);
        $goal->setActive(false);

        return $goal;
    }
}
