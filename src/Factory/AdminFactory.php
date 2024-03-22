<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Admin>
 *
 * @method static Admin|Proxy                     createOne(array $attributes = [])
 * @method static Admin[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Admin[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Admin|Proxy                     find(object|array|mixed $criteria)
 * @method static Admin|Proxy                     findOrCreate(array $attributes)
 * @method static Admin|Proxy                     first(string $sortedField = 'id')
 * @method static Admin|Proxy                     last(string $sortedField = 'id')
 * @method static Admin|Proxy                     random(array $attributes = [])
 * @method static Admin|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Admin[]|Proxy[]                 all()
 * @method static Admin[]|Proxy[]                 findBy(array $attributes)
 * @method static Admin[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Admin[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static AdminRepository|RepositoryProxy repository()
 * @method        Admin|Proxy                     create(array|callable $attributes = [])
 */
final class AdminFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public function promoteRole(string $role): self
    {
        $defaults = $this->getDefaults();

        $roles = array_merge($defaults['roles'], [
            $role,
        ]);

        return $this->addState([
            'roles' => $roles,
        ]);
    }

    protected function getDefaults(): array
    {
        return [
            // add your default values here (https://github.com/zenstruck/foundry#model-factories)
            'email' => self::faker()->email(),
            'roles' => [
                'ROLE_USER',
            ],
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this;
        // ->afterInstantiate(function(Admin $admin): void {})
    }

    protected static function getClass(): string
    {
        return Admin::class;
    }
}
