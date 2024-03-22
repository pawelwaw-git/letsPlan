<?php

declare(strict_types=1);

namespace App\Repeatable;

use App\Contracts\Repeatable;
use Carbon\Carbon;

class EveryWeekRepeatableType implements Repeatable
{
    public function getStartDate(): \DateTime
    {
        return Carbon::now()->endOfWeek()->setTime(0, 0);
    }

    public function getInterval(): \DateInterval
    {
        return new \DateInterval('P7D');
    }
}
