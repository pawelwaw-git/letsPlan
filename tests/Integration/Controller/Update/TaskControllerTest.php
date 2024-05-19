<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller\Update;

use App\Entity\TaskCalendar;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TaskCalendarFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Zenstruck\Foundry\Proxy;

/**
 * @internal
 *
 * @coversNothing
 */
class TaskControllerTest extends WebTestCase
{
    /**
     * @dataProvider InvalidPayloadStatusProvider
     *
     * @param array<string, mixed> $payload
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \JsonException
     * @throws \Exception
     */
    public function testUpdateInvalidStatus(array $payload): void
    {
        $client = static::createClient();
        $task = $this->createTask();

        $client->request(
            'PATCH',
            'tasks/'.$task->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload, JSON_THROW_ON_ERROR)
        );
        $response = $client->getResponse();

        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @dataProvider TaskInvalidIdProvider
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \JsonException
     */
    public function testUpdateInvalidId(string $id): void
    {
        $client = static::createClient();

        $client->request(
            'PATCH',
            'tasks/'.$id,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['status' => false], JSON_THROW_ON_ERROR)
        );
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \JsonException
     * @throws \Exception
     */
    public function testUpdateNotFoundTask(): void
    {
        $client = static::createClient();
        $task = $this->createTask();

        $client->request(
            'PATCH',
            'tasks/'.($task->getId() + 1),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['status' => false], JSON_THROW_ON_ERROR)
        );
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @dataProvider UpdateStatusValidPayloadProvider
     *
     * @param array<string, mixed> $payload
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \JsonException
     * @throws \Exception
     */
    public function testUpdateStatusUpdated(array $payload): void
    {
        $client = static::createClient();
        $task = $this->createTask();

        $client->request(
            'PATCH',
            'tasks/'.$task->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                array_merge([
                    'id' => $task->getId(),
                ], $payload),
                JSON_THROW_ON_ERROR
            )
        );
        $response = $client->getResponse();

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertSame($payload['status'], $task->isIsDone());
    }

    /**
     * @return iterable<array<string, mixed>>
     */
    public function UpdateStatusValidPayloadProvider(): iterable
    {
        yield 'status false not string' => [
            'payload' => [
                'status' => false,
            ],
        ];

        yield 'status true not string' => [
            'payload' => [
                'status' => true,
            ],
        ];
    }

    /**
     * @return iterable<array<string, mixed>>
     */
    public function InvalidPayloadStatusProvider(): iterable
    {
        yield 'status is not boolean' => [
            'payload' => [
                'status' => 'false_string',
            ],
        ];

        yield 'status can not be string' => [
            'payload' => [
                'status' => 'false',
            ],
        ];

        yield 'status is empty' => [
            'payload' => [],
        ];
    }

    /**
     * @return iterable<array<string,string>>
     */
    public function TaskInvalidIdProvider(): iterable
    {
        yield 'id below zero' => ['id' => '-3'];

        yield 'id is not int' => ['id' => '0.3'];

        yield 'id is not zero' => ['id' => '0'];
    }

    /**
     * @throws \Exception
     */
    private function getRandomBoolValue(): bool
    {
        return (bool) (random_int(1, 1000) % 2);
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
            'isDone' => $this->getRandomBoolValue(),
        ]);

        $task->save();

        return $task;
    }
}
