<?php

namespace App\Enum;

use Exception;

enum RepeatableTypes: string
{
    case None = 'none';
    case EveryDay = 'every_day';
    case EveryWeek = 'every_week';
    case EveryMonth = 'every_month';

    public static function getAsKeyValueArray(): array
    {
        $values = [];
        foreach (self::cases() as $case) {
            $values[$case->name] = $case->value;
        }
        return $values;
    }

    public static function randomCase()
    {
        return array_rand(array_flip(self::getAsKeyValueArray()));
    }

    /**
     * return Suitable Interval
     * if None returns null
     * if wrong type then Exception
     *
     * @param string $intervalName
     * @return mixed
     */
    public static function getSuitableInterval(string $intervalName):mixed {
        return match( $intervalName) {
            RepeatableTypes::EveryDay->value => new \DateInterval('P1D'),
            RepeatableTypes::EveryWeek->value => new \DateInterval('P7D'),
            RepeatableTypes::EveryMonth->value => new \DateInterval('P1M'),
            RepeatableTypes::None->value => null,
            default => new RepetableTypeException('No sutiable interval'),
        };
    }
}

Class RepetableTypeException extends Exception {

}
