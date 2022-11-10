<?php

namespace App\Repeatable;

use App\Contracts\IsScheduled;
use App\Contracts\Repeatable;

class EveryWeekRepeatableType implements IsScheduled, Repeatable
{
    public function isScheduled(): bool
    {
        return true;
    }
    public function getStartDate(): \DateTime
    {
        $week = new \DateTime('today');
        while ($week->format("N") != 1) {
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
