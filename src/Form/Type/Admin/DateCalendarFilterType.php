<?php

declare(strict_types=1);

namespace App\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateCalendarFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'Today' => 'today',
                'This month' => 'this_month',
                'Next month' => 'next_month',
            ],
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
