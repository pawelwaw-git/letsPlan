<?php

namespace App\Repeatable;

use App\Contracts\IsScheduled;

class NoneRepeatableType implements IsScheduled
{
    public function isScheduled(): bool
    {
        return false;
    }
}
