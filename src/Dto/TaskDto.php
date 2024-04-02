<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TaskDto
{
    public function __construct(
        #[Assert\NotBlank()]
        #[Assert\Positive()]
        public readonly int $id,
        #[Assert\NotNull()]
        #[Assert\Type(type: 'boolean')]
        public readonly bool $status
    ) {}
}
