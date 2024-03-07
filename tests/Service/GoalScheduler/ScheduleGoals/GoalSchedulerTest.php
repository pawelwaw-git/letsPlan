<?php

namespace App\Tests\Service\GoalScheduler\ScheduleGoals;

use App\Service\GoalScheduler\GoalScheduler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GoalSchedulerTest extends KernelTestCase
{
    private function mockRequest(): RequestStack
    {
        $request = new Request(
            [
                GoalScheduler::QUERY_PARAMS => GoalScheduler::SCHEDULE_ACTION
            ],
            [],
            [],
            [],
            [],
            [],
            'response content'
        );

        $requestStack = new RequestStack();
        $requestStack->push($request);

        return $requestStack;
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
    }


    /**
     * @test
     */
    public function isScheduleGoalsAllowed(): void
    {
        // GIVEN
        $request_stack = $this->mockRequest();

        $container = static::getContainer();

        $container->set(RequestStack::class, $request_stack);
        /**
         * @var GoalScheduler
         */
        $goal_scheduler = $container->get(GoalScheduler::class);

        // WHEN
        $result = $goal_scheduler->isScheduleGoalsAllowed();
        // THEN
        $this->assertTrue($result);
    }
}
