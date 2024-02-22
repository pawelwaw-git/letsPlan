<?php

declare(strict_types=1);

namespace App\Repeatable;

use App\Contracts\Repeatable;

class EveryDayRepeatableType implements Repeatable
{
    public function __construct() {}

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
