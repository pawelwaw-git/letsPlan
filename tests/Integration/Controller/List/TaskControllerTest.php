<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller\List;

use App\Entity\TaskCalendar;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TaskCalendarFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Proxy;

/**
 * @internal
 *
 * @coversNothing
 */
class TaskControllerTest extends WebTestCase
{
    // list with filters (isDone) and (Date)
    // list with pagination

    /**
     * @dataProvider PaginationPerPageDataProvider
     */
    public function testListWithPaginationTotalCorrect(
        int $page,
        int $per_page,
        int $tasks_number,
        int $total_pages
    ): void {
        // GIVEN
        $client = static::createClient();
        $tasks = $this->createTasks($tasks_number);

        // WHEN
        $client->request('GET', 'tasks', [
            'page' => $page,
            'per_page' => $per_page,
        ]);

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        //        TODO implement it
        //        $this->checkStructureResponse();

        $json_response_decoded = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('pagination', $json_response_decoded);
        $this->assertSame($per_page, $json_response_decoded['pagination']['per_page']);
        $this->assertSame($tasks_number, $json_response_decoded['pagination']['total_items']);
        $this->assertSame($total_pages, $json_response_decoded['pagination']['total_pages']);
    }

    public function testSortListWithPagination(): void
    {
        $this->markTestSkipped('implement');
    }

    public function testFilterListWithPagination(): void
    {
        $this->markTestSkipped('implement');
    }

    public function testEmptyResponseRequest(): void
    {
        // GIVEN

        $client = static::createClient();

        // WHEN
        $client->request('GET', 'tasks');

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals([], json_decode($response->getContent(), true)['items']);
    }

    public function testValidRequest(): void
    {
        // GIVEN
        $client = static::createClient();
        $task = $this->createTask();

        // WHEN
        $client->request(
            'GET',
            'tasks'
        );

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame(
            json_encode([
                'items' => [
                    [
                        'id' => $task->getId(),
                        'date' => $task->getDate()->format('Y-m-d'),
                        'goal' => [
                            'id' => $task->getGoal()->getId(),
                            'name' => $task->getGoal()->getName(),
                            'description' => $task->getGoal()->getDescription(),
                            'priority' => $task->getGoal()->getPriority(),
                            'type' => $task->getGoal()->getType(),
                            'repeatable' => $task->getGoal()->getRepeatable(),
                            'active' => $task->getGoal()->isActive(),
                            'last_date_schedule' => $task->getGoal()->getLastDateSchedule(),
                            'possible_to_plan' => $task->getGoal()->isPossibleToPlan(),
                        ],
                        'is_done' => $task->isIsDone(),
                    ],
                ],
                'pagination' => [
                    'current_page' => 1,
                    'has_previous_page' => false,
                    'has_next_page' => false,
                    'per_page' => 10,
                    'total_items' => 1,
                    'total_pages' => 1,
                ],
            ], JSON_THROW_ON_ERROR),
            $response->getContent()
        );
    }

    /**
     * @return iterable<array<string, mixed>>
     */
    public function PaginationPerPageDataProvider(): iterable
    {
        yield 'all in one page' => [
            'page' => 1,
            'per_page' => 10,
            'tasks' => 3,
            'total_pages' => 1,
        ];

        yield 'show second page' => [
            'page' => 2,
            'per_page' => 3,
            'tasks' => 6,
            'total_pages' => 2,
        ];

        yield 'show third page' => [
            'page' => 3,
            'per_page' => 1,
            'tasks' => 6,
            'total_pages' => 6,
        ];
    }

    /**
     * @throws \Exception
     */
    private function createTask(): Proxy|TaskCalendar
    {
        $category = CategoryFactory::createOne();
        $goal = GoalFactory::createOne([
            'Category' => $category,
        ]);

        return TaskCalendarFactory::createOne([
            'Goal' => $goal,
            'isDone' => true,
        ]);
    }

    /**
     * @return array<Proxy|TaskCalendar>
     */
    private function createTasks(int $number): array
    {
        $category = CategoryFactory::createOne();
        $goal = GoalFactory::createOne([
            'Category' => $category,
        ]);

        return TaskCalendarFactory::createMany($number, [
            'Goal' => $goal,
            'isDone' => true,
        ]);
    }
}
