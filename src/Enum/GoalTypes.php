<?php

namespace App\Enum;

enum GoalTypes: string
{
    case Task = 'task';
    case SimpleHabbit = 'simple_habbit';
    case ComplexHabbit = 'complex_habbit';
    case Rule = 'rule';
    case Limit = 'limit';

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
}
