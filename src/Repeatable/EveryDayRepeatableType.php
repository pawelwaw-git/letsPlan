<?php

namespace App\Repeatable;

use App\Contracts\IsScheduled;
use App\Contracts\Repeatable;

class EveryDayRepeatableType implements IsScheduled, Repeatable
{

    public function __construct()
    {
        
    }

    public function isScheduled(): bool
    {
        return true;
    }
    public function getStartDate(): \DateTime
    {
        $today = new \DateTime('today');
        $today->setTime(0, 0, 0);
        return $today;
    }
    public function getInterval(): \DateInterval
    {
        return new \DateInterval('P1D');
    }
}
