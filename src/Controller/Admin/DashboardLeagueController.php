<?php

namespace App\Controller\Admin;

use App\Entity\Goal;
use App\Repository\CategoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;


class DashboardLeagueController extends AbstractDashboardController
{

    private CategoryRepository $category_repository;
    private AdminContextProvider $adminContextProvider;

    public function __construct(CategoryRepository $category_repository, AdminContextProvider $adminContextProvider)
    {
        $this->category_repository = $category_repository;
        $this->adminContextProvider = $adminContextProvider;
    }

    #[Route('/admin/league', name: 'admin_league')]
    public function index(): Response
    {
        $goal = new Goal();
        // TODO this is ugly -> but step by step you should imporove it. -> using TDD of course
        $category_values = [];
        $categories = $this->category_repository->findAll();
        foreach ($categories as $category) {
            $category_values[$category->getName()] = $category;
        }
        $form_filter = $this->createFormBuilder($goal)
            ->add('Category', ChoiceType::class, [
                'choices' => $category_values,
                'label' => 'Category League',
            ])
            ->add('filter', SubmitType::class, ['label' => 'Create League'])
            ->getForm();
        $request = $this->adminContextProvider->getContext()->getRequest();
        $form_filter->handleRequest($request);

        // validation in Controller
        if ($form_filter->isSubmitted() && $form_filter->isValid()) {
            $data = $form_filter->getData();
//            dump($data);
            //if there is existing turnament then open if not create them
        }

        return $this->renderForm('admin/league.html.twig', [
            'form_filter' => $form_filter,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Let\' Plan');
    }

    public function configureMenuItems(): iterable
    {
        return MainMenu::configureMenuItems();
    }
}
