<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller\List;

use App\Entity\TaskCalendar;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TaskCalendarFactory;
use Carbon\Carbon;
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

        $json_response_decoded = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey('pagination', $json_response_decoded);
        $this->assertSame($per_page, $json_response_decoded['pagination']['per_page']);
        $this->assertSame($tasks_number, $json_response_decoded['pagination']['total_items']);
        $this->assertSame($total_pages, $json_response_decoded['pagination']['total_pages']);
    }

    /**
     * @dataProvider SortListPaginationProvider
     *
     * @param array<string,string> $first_task_data
     * @param array<string,string> $second_task_data
     *
     * @throws \JsonException
     * @throws \Exception
     */
    public function testSortListWithPagination(array $first_task_data, array $second_task_data, string $query): void
    {
        // GIVEN
        $client = static::createClient();

        $first_task = $this->createTask([
            'Date' => Carbon::createFromFormat('Y-m-d', $first_task_data['Date']),
        ]);
        $second_task = $this->createTask([
            'Date' => Carbon::createFromFormat('Y-m-d', $second_task_data['Date']),
        ]);

        // WHEN
        $client->request('GET', 'tasks', [
            'sort' => $query,
        ]);

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        //        TODO implement it
        //        $this->checkStructureResponse();

        $json_response_decoded = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($first_task->getId(), $json_response_decoded['items'][0]['id']);
        $this->assertSame($second_task->getId(), $json_response_decoded['items'][1]['id']);
    }

    public function testInvalidSortParamBadRequestExpected(): void
    {
        // GIVEN
        $client = static::createClient();

        $task = $this->createTask();

        // WHEN
        $client->request('GET', 'tasks', [
            'sort' => '-invalidParam',
        ]);

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testFilterListWithPagination(): void
    {
        $this->markTestSkipped('implement');
        // list with filters (isDone) and (Date)
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
     * @return array<array<string, string>>
     */
    public function SortListPaginationProvider(): iterable
    {
        yield 'Date DESC' => [
            'first_task' => [
                'Date' => '2024-05-03',
            ],
            'second_task' => [
                'Date' => '2024-05-02',
            ],
            'query_param' => '-Date',
        ];

        yield 'Date ASC' => [
            'first_task' => [
                'Date' => '2024-05-03',
            ],
            'second_task' => [
                'Date' => '2024-05-04',
            ],
            'query_param' => '+Date',
        ];

        yield 'IsDone ASC' => [
            'first_task' => [
                'IsDone' => true,
                'Date' => '2024-05-04',
            ],
            'second_task' => [
                'IsDone' => false,
                'Date' => '2024-05-04',
            ],
            'query_param' => '+isDone',
        ];

        yield 'IsDone DESC' => [
            'first_task' => [
                'IsDone' => false,
                'Date' => '2024-05-04',
            ],
            'second_task' => [
                'IsDone' => true,
                'Date' => '2024-05-04',
            ],
            'query_param' => '-isDone',
        ];
    }

    /**
     * @param array<string,mixed> $attributes
     *
     * @throws \Exception
     */
    private function createTask(array $attributes = []): Proxy|TaskCalendar
    {
        $category = CategoryFactory::createOne();
        $goal = GoalFactory::createOne([
            'Category' => $category,
        ]);

        return TaskCalendarFactory::createOne(
            array_merge([
                'Goal' => $goal,
                'isDone' => true,
            ], $attributes)
        );
    }

    /**
     * @return Proxy[]|TaskCalendar[]
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
