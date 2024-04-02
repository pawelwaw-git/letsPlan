<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller\Update;

use App\Entity\TaskCalendar;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TaskCalendarFactory;
use ArrayIterator;
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
    protected function setUp(): void
    {
        parent::setUp();

        shell_exec('php bin/console doctrine:database:create --env=test');
        shell_exec('php bin/console doctrine:schema:create --env=test');
    }

    protected function tearDown(): void
    {
        shell_exec('php bin/console doctrine:database:drop --env=test');

        parent::tearDown();
    }

    /**
     * @dataProvider UpdateInvalidPayloadProvider
     * @param array<string, mixed> $payload
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \JsonException
     */
    public function testUpdateInvalidTaskCalendar(array $payload): void
    {
        $client = static::createClient();
        $task = $this->createTask();

        $client->request(
            'PATCH',
            'tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload, JSON_THROW_ON_ERROR)
        );
        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return iterable<array<string, mixed>>
     */
    public function UpdateStatusValidPayloadProvider(): iterable
    {
        yield 'status true' => [
            'payload' => [
                'status' => true,
            ],
        ];

        yield 'status false' => [
            'payload' => [
                'status' => false,
            ],
        ];
    }

    /**
     * @dataProvider UpdateStatusValidPayloadProvider
     * @param array<string, mixed> $payload
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \JsonException
     */
    public function testUpdateIsTaskCalendarStatusUpdated(array $payload): void
    {
        $client = static::createClient();
        $task = $this->createTask();

        $client->request(
            'PATCH',
            'tasks',
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

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($payload['status'], $task->isIsDone());
    }

    /**
     * @return iterable<array<string, mixed>>
     */
    public function UpdateInvalidPayloadProvider(): iterable
    {
        yield 'empty payload' => [
            'payload' => [],
        ];

        yield 'id below zero' => [
            'payload' => [
                'id' => -3,
                'status' => true,
            ],
        ];

        yield 'id is not int' => [
            'payload' => [
                'id' => 0.3,
                'status' => true,
            ],
        ];

        yield 'status is not boolean' => [
            'payload' => [
                'id' => 2,
                //               TODO this test failing => not sure this should be allowed
                //               problem is that I dont know how to use Json Strict deserializer, not strict map string to bool before assertion
                'status' => 'false_string',
            ],
        ];

        yield 'status is empty' => [
            'payload' => [
                'id' => 2,
            ],
        ];
    }

    /**
     * @throws \Exception
     */
    public function getRandomBoolValue(): bool
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
