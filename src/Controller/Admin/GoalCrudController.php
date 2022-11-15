<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Goal;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Enum\GoalTypes;
use App\Enum\RepeatableTypes;
use App\Service\GoalScheduler\GoalScheduler;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

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
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $scheduleTasks = Action::new('Schedule Tasks')
            ->linkToUrl('admin?'.GoalScheduler::QUERY_PARAMS.'='.GoalScheduler::SCHEDULE_ACTION)
            ->createAsGlobalAction();
        $actions->add(Crud::PAGE_INDEX, $scheduleTasks);

        return $actions;
    }
}
