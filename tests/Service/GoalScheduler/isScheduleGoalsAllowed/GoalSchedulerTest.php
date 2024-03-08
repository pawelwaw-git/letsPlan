<?php

declare(strict_types=1);

namespace App\Tests\Service\GoalScheduler\isScheduleGoalsAllowed;

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
     * @return array<string,array<string,bool>>
     */
    public function RequestDataProvider(): iterable
    {
        yield 'Schedule is allowed for valid params in request' => [
            'params' => [GoalScheduler::QUERY_PARAMS => GoalScheduler::SCHEDULE_ACTION],
            'expected' => true,
        ];

        yield 'Schedule is not allowed for invalid params in request' => [
            'params' => [GoalScheduler::QUERY_PARAMS => 'test'],
            'expected' => false,
        ];

        yield 'Schedule is not allowed for empty params in request' => [
            'params' => [],
            'expected' => false,
        ];
    }

    /**
     * @param array<string> $params
     *
     * @dataProvider RequestDataProvider
     *
     * @test
     */
    public function isScheduleGoalsAllowed(array $params, bool $expected): void
    {
        // GIVEN
        $container = static::getContainer();
        $request_stack = $this->createRequest($params);
        $container->set('request_stack', $request_stack);

        /**
         * @var GoalScheduler $goal_scheduler
         */
        $goal_scheduler = $container->get(GoalScheduler::class);

        // WHEN
        $result = $goal_scheduler->isScheduleGoalsAllowed();

        // THEN
        $this->assertSame($expected, $result);
    }

    /**
     * @param array<string> $params
     */
    private function createRequest(array $params): RequestStack
    {
        $request = new Request(
            $params,
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
