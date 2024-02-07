<?php

declare(strict_types=1);

namespace App\Repeatable;

use App\Contracts\IsScheduled;
use App\Contracts\Repeatable;

class EveryMonthRepeatableType implements IsScheduled, Repeatable
{
    public function isScheduled(): bool
    {
        return true;
    }

    public function getStartDate(): \DateTime
    {
        $today = new \DateTime('today');
        $month = new \DateTime('first day of this month');
        if (1 != $today->format('j')) {
            $month->add($this->getInterval());
        }
        $month->setTime(0, 0, 0);

        return $month;
    }

    public function getInterval(): \DateInterval
    {
        return new \DateInterval('P1M');
    }
}
