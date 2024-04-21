<?php

declare(strict_types=1);

namespace App\Serializer;

final class CircularReferenceHandler
{
    public function __invoke($object, $format, $context)
    {
        return $object->getId();
    }
}
