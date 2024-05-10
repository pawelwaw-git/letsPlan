<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Contracts\UserInputException;

class InvalidFilterException extends \Exception implements UserInputException {}
