<?php

namespace App\Tests\Controller\Single;

use App\Controller\TaskController;
use App\Entity\TaskCalendar;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TaskCalendarFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Proxy;

class TaskControllerTest extends WebTestCase
{
    // valid request

    public function testValidRequest(): void
    {
        // GIVEN
        $client = static::createClient();
        $task = $this->createTask();

        // WHEN
        $client->request(
            'GET',
            'tasks/' . $task->getId()
        );

        // THEN
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame(
            json_encode([
                'id' => $task->getId(),
                'goal_id' => $task->getGoal()->getId(),
                'date' => $task->getDate()->format('Y-m-d'),
                'isDone' => $task->isIsDone(),
            ], JSON_THROW_ON_ERROR),
            $response->getContent()
        );
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

    // invalid request (path)

    // get empty data

}
