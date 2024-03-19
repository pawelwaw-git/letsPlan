<?php

declare(strict_types=1);

namespace App\Controller\Admin\Filter;

use App\Form\Type\Admin\DateCalendarFilterType;
use Carbon\Carbon;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

class DateCalendarFilter implements FilterInterface
{
    use FilterTrait;

    public static function new(string $propertyName, bool|string|TranslatableInterface $label = null): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(DateCalendarFilterType::class)
        ;
    }

    public function apply(
        QueryBuilder $queryBuilder,
        FilterDataDto $filterDataDto,
        ?FieldDto $fieldDto,
        EntityDto $entityDto
    ): void {
        if ('today' === $filterDataDto->getValue()) {
            $queryBuilder->andWhere(
                sprintf('%s.%s = :today', $filterDataDto->getEntityAlias(), $filterDataDto->getProperty())
            )
                ->setParameter('today', Carbon::now()->format('Y-m-d'))
            ;
        }
    }
}
