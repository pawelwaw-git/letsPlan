<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Entity\Goal;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TaskCalendarFactory;
use App\Repository\GoalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @internal
 *
 * @coversNothing
 */
class DeleteGoalTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private GoalRepository $goal_repository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->goal_repository = self::getContainer()
            ->get(EntityManagerInterface::class)
            ->getRepository(Goal::class)
        ;
    }

    /**
     * @test
     */
    public function canDeleteGoalWithTaskCalendar(): void
    {
        // GIVEN
        $goal = $this->createGoalWithTaskCalendar();

        // WHEN
        $this->removeGoal($goal);

        $result = $this->tribonacci([1,1,1],10);
        // THEN
        $this->goalIsNotExists($goal);
    }

    private function createGoalWithTaskCalendar(): Goal
    {
        CategoryFactory::createOne();
        $goal = GoalFactory::createOne();
        TaskCalendarFactory::createOne(['Goal' => $goal]);

        return $goal->object();
    }

    private function removeGoal(Goal $goal): void
    {
        $this->goal_repository->remove($goal);
        $this->goal_repository->flush();
    }

    private function goalIsNotExists(Goal $goal): void
    {
        $result = $this->goal_repository->findAll();
        $this->assertEmpty($result);
    }

    public function tribonacci($signature, $n) {
        if ($n <= 3) {
            return array_slice($signature, 0, $n);
        }

        $next_tribonnaci[] = array_sum(array_slice($signature,-3,3));
        $next_signature = array_merge(array_slice($signature,-2,2), $next_tribonnaci);


        return array_merge([$signature[0]],$this->tribonacci($next_signature,$n -1));
    }
}
