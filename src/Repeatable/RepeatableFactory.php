<?php

declare(strict_types=1);

namespace App\Repeatable;

use App\Contracts\Repeatable;
use App\Enum\RepeatableTypes;

class RepeatableFactory
{
    /**
     * @throws RepeatableTypeException
     */
    public static function getSuitableRepeatableType(string $intervalName): Repeatable
    {
        return match ($intervalName) {
            RepeatableTypes::EveryDay->value => new EveryDayRepeatableType(),
            RepeatableTypes::EveryWeek->value => new EveryWeekRepeatableType(),
            RepeatableTypes::EveryMonth->value => new EveryMonthRepeatableType(),
            default => throw new RepeatableTypeException('No suitable interval'),
        };
    }
}
