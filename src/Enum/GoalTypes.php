<?php

namespace App\Enum;

enum GoalTypes: string
{
    case Task = 'task';
    case SimpleHabit = 'simple_habit';
    case ComplexHabit = 'complex_habit';
    case Rule = 'rule';
    case Limit = 'limit';

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
