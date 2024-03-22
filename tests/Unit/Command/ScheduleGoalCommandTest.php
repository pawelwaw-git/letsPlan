<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\ScheduleGoalCommand;
use App\Service\GoalScheduler\GoalScheduler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @coversNothing
 */
class ScheduleGoalCommandTest extends TestCase
{
    public function testExecuteIsCommandSuccessful(): void
    {
        // GIVEN
        $goalScheduler = $this->createMock(GoalScheduler::class);
        $command = new ScheduleGoalCommand($goalScheduler);
        $commandTester = new CommandTester($command);

        // THEN
        $goalScheduler->expects($this->once())->method('scheduleGoals');

        // WHEN
        $commandTester->execute([]);
    }
}
