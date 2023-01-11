<?php

namespace App\Factory;

use App\Entity\Goal;
use App\Entity\Turnament;
use App\Repository\TurnamentRepository;
use DateTime;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Turnament>
 *
 * @method static Turnament|Proxy createOne(array $attributes = [])
 * @method static Turnament[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Turnament[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Turnament|Proxy find(object|array|mixed $criteria)
 * @method static Turnament|Proxy findOrCreate(array $attributes)
 * @method static Turnament|Proxy first(string $sortedField = 'id')
 * @method static Turnament|Proxy last(string $sortedField = 'id')
 * @method static Turnament|Proxy random(array $attributes = [])
 * @method static Turnament|Proxy randomOrCreate(array $attributes = [])
 * @method static Turnament[]|Proxy[] all()
 * @method static Turnament[]|Proxy[] findBy(array $attributes)
 * @method static Turnament[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Turnament[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TurnamentRepository|RepositoryProxy repository()
 * @method Turnament|Proxy create(array|callable $attributes = [])
 */
final class TurnamentFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        $categories = CategoryFactory::createMany(10);
        $goals = GoalFactory::new()->many(0, 10)->create([
            'Category' => CategoryFactory::random(),
        ]);
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'Players' => $goals,
            'CurrentRound' => 1,
            'Finished' => self::faker()->boolean(),
            'updatedAt' => new DateTime("now"),
            'createdAt' => new DateTime("now"),
        ];
    }

    public function allPlayersWithSameCategory(string $categoryName): self
    {
        $category = CategoryFactory::new()->withName($categoryName)->create();
        $goals = GoalFactory::new()->many(0, 10)->create([
            'Category' => $category,
        ]);
        return $this->addState(
            [
                'Players' => $goals,
            ]
        );
    }

    protected function initialize(): self
    {
        return $this->afterInstantiate(function (Turnament $turnament): void {
            $turnament->setRounds($turnament->getMaxRounds());
        });
    }

    protected static function getClass(): string
    {
        return Turnament::class;
    }
}
