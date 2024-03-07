<?php

declare(strict_types=1);

namespace App\Tests\Service\GoalScheduler\ScheduleGoals;

use App\Service\GoalScheduler\GoalScheduler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 *
 * @coversNothing
 */
class GoalSchedulerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
    }

    /**
     * @test
     */
    public function isScheduleGoalsAllowedForRequestWithValidParam(): void
    {
        // GIVEN
        $request_stack = $this->mockValidParamRequest();

        $container = static::getContainer();

        $container->set('request_stack', $request_stack);

        /**
         * @var GoalScheduler $goal_scheduler
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
    public function isScheduleGoalsNotAllowedForRequestWithWrongParam(): void
    {
        // GIVEN
        $request_stack = $this->mockNotValidParamRequest();

        $container = static::getContainer();

        $container->set('request_stack', $request_stack);

        /**
         * @var GoalScheduler $goal_scheduler
         */
        $goal_scheduler = $container->get(GoalScheduler::class);

        // WHEN
        $result = $goal_scheduler->isScheduleGoalsAllowed();

        // THEN
        $this->assertFalse($result);
    }

    private function mockValidParamRequest(): RequestStack
    {
        $request = new Request(
            [GoalScheduler::QUERY_PARAMS => GoalScheduler::SCHEDULE_ACTION],
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

    private function mockNotValidParamRequest(): RequestStack
    {
        $request = new Request(
            [],
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
}
