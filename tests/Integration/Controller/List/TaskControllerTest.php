<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller\List;

use App\Entity\TaskCalendar;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TaskCalendarFactory;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
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
        $this->createTasks($tasks_number);

        // WHEN
        $client->request(Request::METHOD_GET, 'tasks', [
            'page' => $page,
            'per_page' => $per_page,
        ]);

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
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
        $client->request(Request::METHOD_GET, 'tasks', [
            'sort' => $query,
        ]);

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
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

        $this->createTask();

        // WHEN
        $client->request(Request::METHOD_GET, 'tasks', [
            'sort' => '-invalidParam',
        ]);

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @dataProvider FilterListDataProvider
     *
     * @param array<mixed> $tasks_data
     *
     * @throws \JsonException
     */
    public function testFilterListWithPagination(array $tasks_data, string $query, int $result): void
    {
        // GIVEN
        $client = static::createClient();
        $this->createTasksFromArray($tasks_data);

        // THEN
        $client->request(Request::METHOD_GET, 'tasks', [
            'filter' => $query,
        ]);

        // THEN
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($result, count($content['items']));
    }

    /**
     * @dataProvider FilterInvalidParamDataProvider
     *
     * @throws \Exception
     */
    public function testFilterListInvalidParam(string $query): void
    {
        // GIVEN
        $client = static::createClient();
        $this->createTask();

        // WHEN
        $client->request(Request::METHOD_GET, 'tasks', [
            'filter' => $query,
        ]);

        // THEN
        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testEmptyResponseRequest(): void
    {
        // GIVEN

        $client = static::createClient();

        // WHEN
        $client->request(Request::METHOD_GET, 'tasks');

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
            Request::METHOD_GET,
            'tasks'
        );

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
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
     * @return array<mixed> iterable
     */
    public function FilterListDataProvider(): iterable
    {
        yield 'Date Filter gte and lte' => [
            'tasks' => [
                [
                    'Date' => '2024-05-02 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-06-01 00:00:00',
                    'IsDone' => true,
                ],
            ],
            'query' => 'Date[gte]=2024-05-01&Date[lte]=2024-05-31',
            'result' => 1,
        ];

        yield 'Date Filter gte' => [
            'tasks' => [
                [
                    'Date' => '2024-05-02 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-05-04 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-06-01 00:00:00',
                    'IsDone' => true,
                ],
            ],
            'query' => 'Date[gte]=2024-05-04',
            'result' => 2,
        ];

        yield 'Date Filter lte' => [
            'tasks' => [
                [
                    'Date' => '2024-05-02 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-05-04 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-06-01 00:00:00',
                    'IsDone' => true,
                ],
            ],
            'query' => 'Date[lte]=2024-05-04',
            'result' => 2,
        ];

        yield 'Date Filter lt' => [
            'tasks' => [
                [
                    'Date' => '2024-05-02 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-05-04 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-06-01 00:00:00',
                    'IsDone' => true,
                ],
            ],
            'query' => 'Date[lt]=2024-05-04',
            'result' => 1,
        ];

        yield 'Date Filter gt' => [
            'tasks' => [
                [
                    'Date' => '2024-05-02 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-05-04 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-06-01 00:00:00',
                    'IsDone' => true,
                ],
            ],
            'query' => 'Date[gt]=2024-05-04',
            'result' => 1,
        ];

        yield 'Date Filter eq' => [
            'tasks' => [
                [
                    'Date' => '2024-05-02 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-05-04 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-06-01 00:00:00',
                    'IsDone' => true,
                ],
            ],
            'query' => 'Date[eq]=2024-05-04',
            'result' => 1,
        ];

        yield 'IsDone Filter eq No' => [
            'tasks' => [
                [
                    'Date' => '2024-05-04 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-05-04 00:00:00',
                    'IsDone' => false,
                ],
                [
                    'Date' => '2024-05-04 00:00:00',
                    'IsDone' => true,
                ],
            ],
            'query' => 'isDone[eq]=false',
            'result' => 1,
        ];

        yield 'IsDone Filter eq Yes' => [
            'tasks' => [
                [
                    'Date' => '2024-05-04 00:00:00',
                    'IsDone' => true,
                ],
                [
                    'Date' => '2024-05-04 00:00:00',
                    'IsDone' => false,
                ],
                [
                    'Date' => '2024-05-04 00:00:00',
                    'IsDone' => true,
                ],
            ],
            'query' => 'isDone[eq]=true',
            'result' => 2,
        ];
    }

    /**
     * @return iterable<array<string, string>>
     */
    public function FilterInvalidParamDataProvider(): iterable
    {
        yield 'Invalid Filter isDone' => ['query' => 'ISDone[eq]=true'];

        yield 'Invalid Filter Date' => ['query' => 'Datee[eq]=2024-03-12'];

        yield 'Invalid Filter Date Format' => ['query' => 'Date[eq]=2a02-03-12'];

        yield 'Invalid Filter Operator' => ['query' => 'Date[eqi]=2a02-03-12'];
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

    /**
     * @param array<mixed> $data
     *
     * @return Proxy[]|TaskCalendar[]
     */
    private function createTasksFromArray(array $data): array
    {
        $category = CategoryFactory::createOne();
        $goal = GoalFactory::createOne([
            'Category' => $category,
        ]);

        $tasks = [];
        foreach ($data as $item) {
            $task = TaskCalendarFactory::createOne(
                ['Goal' => $goal, 'isDone' => true, 'Date' => Carbon::createFromFormat('Y-m-d H:i:s', $item['Date']), 'IsDone' => $item['IsDone']]
            );
            $tasks[] = $task;
        }

        return $tasks;
    }
}
