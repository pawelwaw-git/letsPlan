<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Category>
 *
 * @method static Category|Proxy                     createOne(array $attributes = [])
 * @method static Category[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Category[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Category|Proxy                     find(object|array|mixed $criteria)
 * @method static Category|Proxy                     findOrCreate(array $attributes)
 * @method static Category|Proxy                     first(string $sortedField = 'id')
 * @method static Category|Proxy                     last(string $sortedField = 'id')
 * @method static Category|Proxy                     random(array $attributes = [])
 * @method static Category|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Category[]|Proxy[]                 all()
 * @method static Category[]|Proxy[]                 findBy(array $attributes)
 * @method static Category[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Category[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static CategoryRepository|RepositoryProxy repository()
 * @method        Category|Proxy                     create(array|callable $attributes = [])
 */
final class CategoryFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'Name' => self::faker()->text(),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Category::class;
    }
}
