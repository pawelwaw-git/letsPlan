<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Goal;
use App\Entity\TaskCalendar;
use App\Repository\TaskCalendarRepository;
use App\Service\GoalScheduler\GoalScheduler;
use App\Service\GoalScheduler\TaskChartDatasetGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardLeagueController extends AbstractDashboardController
{

    #[Route('/admin/league', name: 'admin_league')]
    public function index(): Response
    {

        return $this->render('admin/league.html.twig', [
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
