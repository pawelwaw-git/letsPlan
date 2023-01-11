<?php

namespace App\DataFixtures;

use App\Factory\AdminFactory;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TurnamentFactory;
use App\Service\GoalScheduler\GoalScheduler;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private GoalScheduler $goalScheduler;

    public function __construct(GoalScheduler $goalScheduler)
    {
        $this->goalScheduler = $goalScheduler;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers();
        $this->loadCategories();
        $this->loadGoals();
        $this->goalScheduler->setPermissionToSchedule(true);
        $this->goalScheduler->scheduleGoals();
        $manager->flush();
    }

    private function loadUsers()
    {
        AdminFactory::new()
            ->withAttributes([
                'email' => 'superadmin@example.com',
                'password' => '$2y$13$ZG5Oyb39j1HjQk8K9VNrtOQweg4G/947cCHlGXNzW2dtReVhyDDBG',
                // 'password' => 'adminpass',
            ])
            ->promoteRole('ROLE_SUPER_ADMIN')
            ->create();
        AdminFactory::new()
            ->withAttributes([
                'email' => 'admin@example.com',
                'password' => '$2y$13$ZG5Oyb39j1HjQk8K9VNrtOQweg4G/947cCHlGXNzW2dtReVhyDDBG',
                // 'password' => 'adminpass',
            ])
            ->promoteRole('ROLE_ADMIN')
            ->create();
        AdminFactory::new()
            ->withAttributes([
                'email' => 'moderatoradmin@example.com',
                'password' => '$2y$13$ZG5Oyb39j1HjQk8K9VNrtOQweg4G/947cCHlGXNzW2dtReVhyDDBG',
                // 'password' => 'adminpass',
            ])
            ->promoteRole('ROLE_MODERATOR')
            ->create();
        AdminFactory::new()
            ->withAttributes([
                'email' => 'tisha@symfonycasts.com',
                // 'password' => 'tishapass',
                'password' => '$2y$13$jeJsCrAHWizlS3gPM332.uMZ/YIFv0quIMOjrhehkBAxsdhVlemq6',
            ])
            ->create();
    }

    public function loadCategories()
    {
        $categories = ['God', 'Health', 'Finance', 'Carrier', 'Hobby', 'Development'];
        foreach ($categories as $category)
            CategoryFactory::new()
                ->withAttributes([
                    'name' => $category,
                ])->create();
    }

    public function loadTurnaments() {

        TurnamentFactory::new()
            ->many(3)
            ->create(function (){
                $goals = GoalFactory::randomRange(2,5);
                return [ 'Players' => $goals , ] ;
            });
    }

    /**
     * @return void
     */
    public function loadGoals(): void
    {
        GoalFactory::createMany(25, function () {
            return GoalFactory::getProperTypeAndRepatableValues();
        });
    }
}
