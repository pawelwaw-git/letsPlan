<?php

namespace App\Tests\Unit\Turnament;

use App\Entity\Goal;
use App\Factory\TurnamentFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use App\Entity\Turnament;
use Zenstruck\Foundry\Proxy;


class TurnamentTest extends WebTestCase
{
    use Factories;

    /**
     * @dataProvider getMaxRoundsTestValues
     */
    public function testGetMaxRounds(?int $there_is_n_players, $expected_rounds)
    {
        //given
        $turnament = $this->CreateTurnamentWithPlayers($there_is_n_players);
        //when
        $count = $turnament->getMaxRounds();
        //then
        $this->assertSame($count, $expected_rounds);
    }

    /**
     * @param int|null $there_is_n_players
     * @return \App\Entity\Turnament|\Zenstruck\Foundry\Proxy
     */
    public function CreateTurnamentWithPlayers(?int $there_is_n_players): Turnament|Proxy
    {
        $turnament = TurnamentFactory::new()->withoutPersisting()->create();
        $goal = $this->createMock(Goal::class);
        $i = 0;
        while ($i < $there_is_n_players) {
            $pl = clone $goal;
            $turnament->addPlayer($pl);
            $i++;
        }
        return $turnament;
    }

    private function getMaxRoundsTestValues()
    {
        return [
            ['players' => 0, 'excpected' => 0],
            ['players' => 1, 'excpected' => 1],
            ['players' => 2, 'excpected' => 1],
            ['players' => 3, 'excpected' => 3],
            ['players' => 4, 'excpected' => 6],
            ['players' => 5, 'excpected' => 10],
            ['players' => 6, 'excpected' => 15],
        ];
    }
}
