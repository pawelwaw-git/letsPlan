<?php

namespace App\Repeatable;

use App\Enum\RepeatableTypes;
use App\Repeatable\EveryDayRepeatableType;
use App\Repeatable\EveryMonthRepeatableType;
use App\Repeatable\EveryWeekRepeatableType;
use App\Repeatable\NoneRepeatableType;

class RepeatableFactory
{
    public static function getSuitableRepeatableType(string $intervalName)
    {
        return match ($intervalName) {
            RepeatableTypes::EveryDay->value => new EveryDayRepeatableType(),
            RepeatableTypes::EveryWeek->value => new EveryWeekRepeatableType(),
            RepeatableTypes::EveryMonth->value => new EveryMonthRepeatableType(),
            RepeatableTypes::None->value => new NoneRepeatableType(),
            default => new RepetableTypeException('No sutiable interval'),
        };
    }
}
class RepetableTypeException extends \Exception
{
    
}
