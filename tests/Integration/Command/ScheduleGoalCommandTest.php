<?php

declare(strict_types=1);

namespace App\Tests\Integration\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @coversNothing
 */
class ScheduleGoalCommandTest extends KernelTestCase
{
    public function testExecuteIsCommandSuccessful(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:goal-schedule');

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $this->assertSame('Active Goals was scheduled', $commandTester->getDisplay());
    }
}
