<?php

declare(strict_types=1);

namespace App\Repeatable;

use App\Contracts\Repeatable;
use Carbon\Carbon;

class EveryMonthRepeatableType implements Repeatable
{
    public function getStartDate(): \DateTime
    {
        $month = new Carbon('first day of this month');
        if (intval(Carbon::now()->format('j')) !== 1) {
            $month->add($this->getInterval());
        }

        return $month->setTime(0, 0);
    }

    public function getInterval(): \DateInterval
    {
        return new \DateInterval('P1M');
    }
}
