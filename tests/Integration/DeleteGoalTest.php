<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Entity\Goal;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Repository\GoalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

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
            ->getRepository(Goal::class);
    }

    /**
     * @test
     */
    public function somethingToTest(): void
    {
        //GIVEN
        $goal = $this->createGoalWithTaskCalendar();

        // WHEN
        $this->removeGoal($goal);

        // THEN
        $this->goalIsNotExists($goal);
    }

    public function createGoalWithTaskCalendar(): Goal
    {
        CategoryFactory::createOne();
        return GoalFactory::createOne()->object();
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
}