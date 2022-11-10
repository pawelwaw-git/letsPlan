<?php

namespace App\Contracts;

interface Repeatable {
    public function getStartDate():\DateTime;
    public function getInterval():\DateInterval;
}