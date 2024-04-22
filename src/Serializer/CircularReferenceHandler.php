<?php

declare(strict_types=1);

namespace App\Serializer;

final class CircularReferenceHandler
{
    /**
     * @param array<string> $context
     */
    public function __invoke(object $object, ?string $format, array $context): int
    {
        return $object->getId();
    }
}
