<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TaskDto
{
    public function __construct(
        #[Assert\NotNull]
        //        #[Assert\Regex("/['\"]?true|false['\"]?/i")]
        #[Assert\Type(type: 'bool')]
        public readonly bool $status
    ) {}
}
