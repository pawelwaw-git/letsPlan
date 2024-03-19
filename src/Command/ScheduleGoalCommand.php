<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\GoalScheduler\GoalScheduler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ScheduleGoalCommand',
    description: 'command to schedule Active Goals',
)]
class ScheduleGoalCommand extends Command
{
    protected static $defaultName = 'app:goal-schedule';

    public function __construct(private readonly GoalScheduler $goal_scheduler)
    {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->goal_scheduler->scheduleGoals();

        $output->write('Active Goals was scheduled');

        return Command::SUCCESS;
    }
}
