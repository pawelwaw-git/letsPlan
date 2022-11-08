<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\DataFixtures\AppFixtures;
use App\Enum\RepeatableTypes;
use App\Factory\GoalFactory;
use App\Repository\GoalRepository;
use App\Repository\TaskCalendarRepository;
use App\Service\GoalScheduler;
use Behat\Behat\Context\Context;
use DateInterval;

use function PHPUnit\Framework\assertEquals;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class TasksContext implements Context
{
    protected TaskCalendarRepository $taskCalendarRepository;
    protected GoalRepository $goalRepository;

    public function __construct(TaskCalendarRepository $taskCalendarRepository, GoalRepository $goalRepository)
    {
        $this->taskCalendarRepository = $taskCalendarRepository;
        $this->goalRepository = $goalRepository;
    }

    /**
     * @Given There are different goal types
     */
    public function thereAreDifferentGoalTypes()
    {
        $fixtures = new AppFixtures();
        $fixtures->loadCategories();
        GoalFactory::createSequence(
            [
                ['Repeatable' => RepeatableTypes::EveryDay->value, 'Active' => true],
                ['Repeatable' => RepeatableTypes::EveryWeek->value, 'Active' => true],
                ['Repeatable' => RepeatableTypes::EveryMonth->value, 'Active' => true],
                ['Repeatable' => RepeatableTypes::None->value, 'Active' => true],
            ]
        );
    }
    /**
     * @Then there and planed tasks in db
     */
    public function thereAndPlanedTasksInDb()
    {
        $today = new \DateTime('today');
        $end = clone $today;
        $end->add(new DateInterval(GoalScheduler::SCHEDULE_DATEINTERVAL_TEXT));
        $days_diff = $end->diff($today);

        $expected_days = $this->taskCalendarRepository->getQuantityOfTasksTypes(RepeatableTypes::EveryDay->value);
        $expected_weeks = $this->taskCalendarRepository->getQuantityOfTasksTypes(RepeatableTypes::EveryWeek->value);
        $expected_months = $this->taskCalendarRepository->getQuantityOfTasksTypes(RepeatableTypes::EveryMonth->value);
        $expected_none = $this->taskCalendarRepository->getQuantityOfTasksTypes(RepeatableTypes::None->value);

        assertEquals($expected_days, $days_diff->days);
        assertEquals($expected_weeks, ceil($days_diff->days / 7));
        assertEquals($expected_months, $days_diff->format("%m"));
        assertEquals($expected_none, 0);
    }

    /**
     * @Given truncate tasks and reset last_date_schedule
     */
    public function truncateTasksAndResetLastDateSchedule()
    {
        // set last_date_schedule to null
        $goals = $this->goalRepository->findAll();
        foreach ($goals as $goal) {
            $goal->setLastDateSchedule(NULL);
        }
        $this->goalRepository->flush();
        // delete all tasks
        $tasks = $this->taskCalendarRepository->findAll();
        foreach ($tasks as $task) {
            $this->taskCalendarRepository->remove($task);
        }
        $this->taskCalendarRepository->flush();
    }
}
