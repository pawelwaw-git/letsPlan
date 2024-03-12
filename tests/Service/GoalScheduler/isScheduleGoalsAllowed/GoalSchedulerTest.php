<?php

declare(strict_types=1);

namespace App\Tests\Service\GoalScheduler\isScheduleGoalsAllowed;

use App\Entity\Goal;
use App\Repeatable\EveryDayRepeatableType;
use App\Repository\GoalRepository;
use App\Repository\TaskCalendarRepository;
use App\Service\GoalScheduler\GoalScheduler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 *
 * @coversNothing
 */
class GoalSchedulerTest extends TestCase
{
   public function testIfGoalsAreScheduled(): void
   {
       $goal = $this->givenSchedulableGoal();
       $taskCalendarRepository = $this->createTaskCalendarMock();

       $goalScheduler = $this->createGoalScheduler($goal, $taskCalendarRepository);

       $this->thenTaskCalendarIsSaved($taskCalendarRepository);

       $this->whenScheduleGoal($goalScheduler);

       $this->thenGetLastScheduleIsUpdated($goal);
   }

   private function createGoalRepositoryMock(Goal $goal)
   {
       $goalRepository = $this->createMock(GoalRepository::class);

       $goalRepository
           ->method('findGoalsToSchedule')
           ->willReturn([
               $goal
           ]);

       return $goalRepository;
   }

   private function createGoalScheduler(Goal $goal, TaskCalendarRepository $taskCalendarRepository): GoalScheduler
   {
       $goalRepository = $this->createGoalRepositoryMock($goal);

       $logger = $this->createMock(LoggerInterface::class);

       $goalScheduler = new GoalScheduler(
           $goalRepository,
           $taskCalendarRepository,
           $this->createMock(RequestStack::class),
           $logger
       );

       return $goalScheduler;
   }

   private function givenSchedulableGoal(): Goal
   {
       $goal = new Goal();
       $goal->setRepeatable(EveryDayRepeatableType::class);
       $goal->setActive(true);

       return $goal;
   }

   private function whenScheduleGoal(GoalScheduler $goalScheduler): void
   {
       $goalScheduler->scheduleGoals();
   }

    /**
     * @param TaskCalendarRepository|MockObject $taskCalendarRepository
     * @return void
     */
   private function thenTaskCalendarIsSaved(TaskCalendarRepository $taskCalendarRepository): void
   {
       $taskCalendarRepository
           ->expects($this->once())
           ->method('save');
   }

   private function thenGetLastScheduleIsUpdated(Goal $goal): void
   {
       $this->assertEquals(/* TODO: Dopisać expected value */, $goal->getLastDateSchedule());
   }

    /**
     * @return TaskCalendarRepository|(TaskCalendarRepository&object&MockObject)|(TaskCalendarRepository&MockObject)|(object&MockObject)|MockObject
     */
    public function createTaskCalendarMock(): TaskCalendarRepository|MockObject|object
    {
        $taskCalendarRepository = $this->createMock(TaskCalendarRepository::class);
        return $taskCalendarRepository;
    }
}
