<?php

declare(strict_types=1);

namespace App\Enum;

enum RepeatableTypes: string
{
    case None = 'none';
    case EveryDay = 'every_day';
    case EveryWeek = 'every_week';
    case EveryMonth = 'every_month';

    /**
     * @return array<string>
     */
    public static function getAsKeyValueArray(): array
    {
        $values = [];
        foreach (self::cases() as $case) {
            $values[$case->name] = $case->value;
        }

        return $values;
    }

    public static function randomCase(): string
    {
        return array_rand(array_flip(self::getAsKeyValueArray()));
    }
}
