<?php

namespace App\Tests\Integration\Controller\Update;

use App\Entity\TaskCalendar;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TaskCalendarFactory;
use App\Repository\TaskCalendarRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\HttpClient;
use Zenstruck\Foundry\Proxy;

class TaskControllerTest extends KernelTestCase
{

    /**
     * @return void
     * @dataProvider UpdateValidPayloadProvider
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */

    public function testUpdateValidTaskCalendar(array $payload): void
    {
        $kernel = self::bootKernel();
        $task = $this->createTask();

        $httpClient = HttpClient::createForBaseUri('http://lets-plan-php:80/');
        $response = $httpClient->request('PATCH', 'tasks/' . $task->getId(), $payload);

        $this->assertSame(200, $response->getStatusCode());
//        $this->assertEquals([], $response->getContent());

        $this->assertStatusForTask($task->getId(), $payload['status'] ?? $task->isIsDone(), $kernel);
    }

    private function createTask(): TaskCalendar|Proxy
    {
        $category = CategoryFactory::createOne();
        $goal = GoalFactory::createOne([
            'Category' => $category,
        ]);
        $task = TaskCalendarFactory::createOne([
            'Goal' => $goal,
            'isDone' => false,
        ]);
        return $task;
    }

    public function UpdateValidPayloadProvider(): iterable
    {
        yield 'empty payload' => [
            'payload' => [],
        ];

        yield 'status true' => [
            'payload' => ['isDone' => true],
        ];

        yield 'status false' => [
            'payload' => ['isDone' => false],
        ];
    }

    private function assertStatusForTask(int $task_id, bool $status, $kernel): void
    {
        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $task = $entityManager
            ->getRepository(TaskCalendar::class)
            ->find($task_id);

        $this->assertSame($status, $task->isIsDone());
    }
}
