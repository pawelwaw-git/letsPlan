<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Goal;
use App\Enum\GoalTypes;
use App\Enum\RepeatableTypes;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GoalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Goal::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('Name'),
            Field::new('Priority'),
            ChoiceField::new('Type')->setChoices(
                GoalTypes::getAsKeyValueArray()
            )->renderAsNativeWidget(),
            ChoiceField::new('Repeatable')->setChoices(
                RepeatableTypes::getAsKeyValueArray()
            )->renderAsNativeWidget(),
            AssociationField::new('Category')
                ->renderAsNativeWidget()
                ->setFormTypeOption('by_reference', true),
            TextEditorField::new('Description'),
            BooleanField::new('Active'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $scheduleTasks = Action::new('Schedule Tasks')
            ->linkToUrl('admin?'.DashboardController::QUERY_PARAMS.'='.DashboardController::SCHEDULE_ACTION)
            ->createAsGlobalAction()
        ;
        $actions->add(Crud::PAGE_INDEX, $scheduleTasks);

        return $actions;
    }
}
