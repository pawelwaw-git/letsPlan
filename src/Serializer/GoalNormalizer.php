<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Goal;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class GoalNormalizer implements ContextAwareNormalizerInterface
{
    public function __construct(
        private readonly ObjectNormalizer $normalizer,
    ) {}

    /**
     * @param mixed         $object
     * @param array<string> $context
     *
     * @return array<string>
     *
     * @throws ExceptionInterface
     */
    public function normalize($object, ?string $format = null, array $context = []): array|int
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        if (!is_array($data)) {
            return $data;
        }

        return [
            'id' => $object->getid(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'priority' => $object->getPriority(),
            'type' => $object->getType(),
            'repeatable' => $object->getRepeatable(),
            'active' => $object->isActive(),
            'last_date_schedule' => $object->getLastDateSchedule(),
            'possible_to_plan' => $object->isPossibleToPlan(),
        ];
    }

    /**
     * @param array<string> $context
     * @param mixed         $data
     */
    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Goal;
    }
}
