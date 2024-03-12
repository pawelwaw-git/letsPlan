<?php

namespace App\Command;

use App\Service\GoalScheduler\GoalScheduler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScheduleGoal extends Command
{
    private const COMMAND_NAME = 'goals:schedule';

    private GoalScheduler $goalScheduler;

    protected static $defaultName = self::COMMAND_NAME;

    public function __construct(GoalScheduler $goalScheduler)
    {
        parent::__construct(self::COMMAND_NAME);

        $this->goalScheduler = $goalScheduler;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->goalScheduler->scheduleGoals();
    }
}