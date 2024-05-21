<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller\Single;

use App\Entity\TaskCalendar;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TaskCalendarFactory;
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
    public function testValidRequest(): void
    {
        // GIVEN
        $client = static::createClient();
        $task = $this->createTask();

        // WHEN
        $client->request(
            Request::METHOD_GET,
            'tasks/'.$task->getId()
        );

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame(
            json_encode([
                'id' => $task->getId(),
                'goal_id' => $task->getGoal()->getId(),
                'date' => $task->getDate()->format('Y-m-d'),
                'is_done' => $task->isIsDone(),
            ], JSON_THROW_ON_ERROR),
            $response->getContent()
        );
    }

    /**
     * @dataProvider InvalidTaskDataProvider
     */
    public function testInvalidRequest(string $invalid_task): void
    {
        // GIVEN
        $client = static::createClient();

        // WHEN
        $client->request(
            Request::METHOD_GET,
            'tasks/'.$invalid_task
        );

        // THEN
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @return iterable<array<int, string>>
     */
    public function InvalidTaskDataProvider(): iterable
    {
        yield 'invalid int' => ['3'];

        yield 'negative value' => ['-3'];

        yield 'string value' => ['some_string'];
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

        $task = TaskCalendarFactory::createOne([
            'Goal' => $goal,
            'isDone' => true,
        ]);

        $task->save();

        return $task;
    }
}
