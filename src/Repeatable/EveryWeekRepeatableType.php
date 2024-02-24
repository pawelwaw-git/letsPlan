<?php

declare(strict_types=1);

namespace App\Repeatable;

use App\Contracts\Repeatable;

class EveryWeekRepeatableType implements Repeatable
{
    public function getStartDate(): \DateTime
    {
        $week = new \DateTime('today');
        while ($week->format('N') != 1) {
            $week->modify('+1 day');
        }
        $week->setTime(0, 0, 0);

        return $week;
    }

    public function getInterval(): \DateInterval
    {
        return new \DateInterval('P7D');
    }
}
