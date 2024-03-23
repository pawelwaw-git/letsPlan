<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\TaskCalendar;
use App\Repository\TaskCalendarRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<TaskCalendar>
 *
 * @method        Proxy|TaskCalendar                     create(array|callable $attributes = [])
 * @method static TaskCalendar|Proxy                     createOne(array $attributes = [])
 * @method static TaskCalendar|Proxy                     find(object|array|mixed $criteria)
 * @method static TaskCalendar|Proxy                     findOrCreate(array $attributes)
 * @method static TaskCalendar|Proxy                     first(string $sortedField = 'id')
 * @method static TaskCalendar|Proxy                     last(string $sortedField = 'id')
 * @method static TaskCalendar|Proxy                     random(array $attributes = [])
 * @method static TaskCalendar|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TaskCalendarRepository|RepositoryProxy repository()
 * @method static TaskCalendar[]|Proxy[]                 all()
 * @method static TaskCalendar[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static TaskCalendar[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static TaskCalendar[]|Proxy[]                 findBy(array $attributes)
 * @method static TaskCalendar[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static TaskCalendar[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TaskCalendarFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'Date' => self::faker()->dateTime(),
            'isDone' => self::faker()->boolean(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return TaskCalendar::class;
    }
}
