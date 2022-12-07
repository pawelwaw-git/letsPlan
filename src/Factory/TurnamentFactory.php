<?php

namespace App\Factory;

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
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
//            'Rounds' => self::faker()->randomNumber(),
//            'CurrentRound' => self::faker()->randomNumber(),
            'Finished' => self::faker()->boolean(),
            'updatedAt' => new DateTime("now"),
            'createdAt' => new DateTime("now"),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this// ->afterInstantiate(function(Turnament $turnament): void {})
            ;
    }

    protected static function getClass(): string
    {
        return Turnament::class;
    }
}
