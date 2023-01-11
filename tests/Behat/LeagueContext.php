<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\DataFixtures\AppFixtures;
use App\Factory\CategoryFactory;
use App\Factory\GoalFactory;
use App\Factory\TurnamentFactory;
use App\Repository\GoalRepository;
use App\Repository\TurnamentRepository;
use App\Service\GoalScheduler\GoalScheduler;
use Behat\Behat\Context\Context;

use Doctrine\ORM\Mapping as ORM;
use function PHPUnit\Framework\assertTrue;
use function Zenstruck\Foundry\faker;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
#[ORM\Embeddable]
final class LeagueContext implements Context
{
    private TurnamentRepository $turnamentRepository;
    private GoalRepository $goalRepository;

    /**
     * @param TurnamentRepository $turnamentRepository
     * @param GoalRepository $goalRepository
     * @param GoalScheduler $goalScheduler
     */
    public function __construct(TurnamentRepository $turnamentRepository, GoalRepository $goalRepository)
    {
        $this->turnamentRepository = $turnamentRepository;
        $this->goalRepository = $goalRepository;
    }

    /**
     * @Given There is no Turnament in db
     */
    public function thereIsNoTurnamentInDb()
    {
        $turnaments = $this->turnamentRepository->findAll();
        foreach ($turnaments as $turnament) {
            $this->turnamentRepository->remove($turnament);
        }

        $goals = $this->goalRepository->findAll();
        foreach ($goals as $goal) {
            $this->goalRepository->remove($goal);
        }

        $this->goalRepository->flush();
        $this->turnamentRepository->flush();
    }

    /**
     * @When I create new Turnament for Category :category
     */
    public function iCreateNewTurnament($category)
    {
        $this->thereIsNoTurnamentInDb();
        TurnamentFactory::new()->allPlayersWithSameCategory($category)->many(1)->create();
    }

    /**
     * @Then new turnament will be created
     */
    public function newTurnamentWillBeCreated()
    {
        TurnamentFactory::assert()->count(1);
    }

    /**
     * @Then existing turnament can be opened
     */
    public function existingTurnamentCanBeOpened()
    {
        assertTrue(False);
    }

}
