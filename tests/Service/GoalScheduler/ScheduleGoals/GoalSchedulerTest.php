<?php

namespace App\Tests\Service\GoalScheduler\ScheduleGoals;

use App\Service\GoalScheduler\GoalScheduler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class GoalSchedulerTest extends KernelTestCase
{
    private function mockValidParamRequest(): RequestStack
    {
        $request = new Request(
            [GoalScheduler::QUERY_PARAMS => GoalScheduler::SCHEDULE_ACTION],
            [], [], [], [], [],
            'response content'
        );

        $requestStack = new RequestStack();
        $requestStack->push($request);

        return $requestStack;
    }

    private function mockNotValidParamRequest(): RequestStack
    {
        $request = new Request(
            [], [], [], [], [], [],
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
    public function isScheduleGoalsAllowed_forRequestWithValidParam(): void
    {
        // GIVEN
        $request_stack = $this->mockValidParamRequest();

        $container = static::getContainer();

        $container->set('request_stack', $request_stack);
        /**
         * @var $goal_scheduler GoalScheduler
         */
        $goal_scheduler = $container->get(GoalScheduler::class);

        // WHEN
        $result = $goal_scheduler->isScheduleGoalsAllowed();

        // THEN
        $this->assertTrue($result);
    }


    /**
     * @test
     */
    public function isScheduleGoalsNotAllowed_forRequestWithWrongParam(): void
    {
        // GIVEN
        $request_stack = $this->mockNotValidParamRequest();

        $container = static::getContainer();

        $container->set('request_stack', $request_stack);
        /**
         * @var $goal_scheduler GoalScheduler
         */
        $goal_scheduler = $container->get(GoalScheduler::class);

        // WHEN
        $result = $goal_scheduler->isScheduleGoalsAllowed();

        // THEN
        $this->assertFalse($result);
    }
}
