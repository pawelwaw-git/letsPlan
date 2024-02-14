<?php

declare(strict_types=1);

namespace App\Contracts;

interface IsScheduled
{
    public function isScheduled(): bool;
}
