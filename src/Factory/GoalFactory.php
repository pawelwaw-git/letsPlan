<?php

namespace App\Factory;

use App\Entity\Goal;
use App\Enum\GoalTypes;
use App\Enum\RepeatableTypes;
use App\Repository\GoalRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Goal>
 *
 * @method static Goal|Proxy createOne(array $attributes = [])
 * @method static Goal[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Goal[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Goal|Proxy find(object|array|mixed $criteria)
 * @method static Goal|Proxy findOrCreate(array $attributes)
 * @method static Goal|Proxy first(string $sortedField = 'id')
 * @method static Goal|Proxy last(string $sortedField = 'id')
 * @method static Goal|Proxy random(array $attributes = [])
 * @method static Goal|Proxy randomOrCreate(array $attributes = [])
 * @method static Goal[]|Proxy[] all()
 * @method static Goal[]|Proxy[] findBy(array $attributes)
 * @method static Goal[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Goal[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static GoalRepository|RepositoryProxy repository()
 * @method Goal|Proxy create(array|callable $attributes = [])
 */
final class GoalFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
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
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Goal $goal): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Goal::class;
    }

    public static function getProperTypeAndRepatableValues()
    {
        $random_type = GoalTypes::randomCase();
        $repeatble_values = match ($random_type) {
            GoalTypes::Rule->value, GoalTypes::Limit->value, GoalTypes::Task->value => [RepeatableTypes::None->value],
            GoalTypes::SimpleHabbit->value, GoalTypes::ComplexHabbit->value => [RepeatableTypes::EveryDay->value, RepeatableTypes::EveryWeek->value, RepeatableTypes::EveryMonth->value],
        };

        return [
            'Type' => $random_type,
            'Repeatable' => self::faker()->randomElement($repeatble_values)
        ];
    }
}
