<?php

declare(strict_types=1);

namespace App\Contracts;

interface Repeatable
{
    public function getStartDate(): \DateTime;

    public function getInterval(): \DateInterval;
}
