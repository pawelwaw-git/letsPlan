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
    protected function setUp(): void
    {
        parent::setUp();

        shell_exec('php bin/console doctrine:database:create --env=test');
        shell_exec('php bin/console doctrine:schema:create --env=test');
    }

    protected function tearDown(): void
    {
        shell_exec('php bin/console doctrine:database:drop --env=test');

        parent::tearDown();
    }

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
