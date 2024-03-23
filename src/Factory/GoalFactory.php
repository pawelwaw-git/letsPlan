<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Goal;
use App\Enum\GoalTypes;
use App\Enum\RepeatableTypes;
use App\Repository\GoalRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Goal>
 *
 * @method static Goal|Proxy                     createOne(array $attributes = [])
 * @method static Goal[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Goal[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Goal|Proxy                     find(object|array|mixed $criteria)
 * @method static Goal|Proxy                     findOrCreate(array $attributes)
 * @method static Goal|Proxy                     first(string $sortedField = 'id')
 * @method static Goal|Proxy                     last(string $sortedField = 'id')
 * @method static Goal|Proxy                     random(array $attributes = [])
 * @method static Goal|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Goal[]|Proxy[]                 all()
 * @method static Goal[]|Proxy[]                 findBy(array $attributes)
 * @method static Goal[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Goal[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static GoalRepository|RepositoryProxy repository()
 * @method        Goal|Proxy                     create(array|callable $attributes = [])
 */
final class GoalFactory extends ModelFactory
{
    /**
     * @return array<string, string>
     */
    public static function getProperTypeAndRepeatableValues(): array
    {
        $random_type = GoalTypes::randomCase();
        $repeatable_values = match ($random_type) {
            GoalTypes::Rule->value, GoalTypes::Limit->value, GoalTypes::Task->value => [RepeatableTypes::None->value],
            GoalTypes::SimpleHabit->value, GoalTypes::ComplexHabit->value => [RepeatableTypes::EveryDay->value, RepeatableTypes::EveryWeek->value, RepeatableTypes::EveryMonth->value],
            default => [],
        };

        return [
            'Type' => $random_type,
            'Repeatable' => self::faker()->randomElement($repeatable_values),
        ];
    }

    protected function getDefaults(): array
    {
        return [
            'Name' => self::faker()->word(),
            'Priority' => self::faker()->numberBetween(1, 20),
            'Category ' => CategoryFactory::random(),
            'Type' => GoalTypes::randomCase(),
            'Repeatable' => RepeatableTypes::randomCase(),
            'Description' => self::faker()->text(),
            'Active' => self::faker()->numberBetween(0, 1),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Goal::class;
    }
}
