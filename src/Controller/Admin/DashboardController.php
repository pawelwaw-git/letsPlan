<?php

declare(strict_types=1);

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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractDashboardController
{
    public const QUERY_PARAMS = 'goal_scheduler_param';
    public const SCHEDULE_ACTION = 'schedule';
    private GoalScheduler $scheduler;
    private ChartBuilderInterface $chartBuilder;
    private TaskCalendarRepository $taskCalendarRepository;
    private TaskChartDatasetGenerator $taskChartDatasetFactory;

    public function __construct(
        GoalScheduler $scheduler,
        ChartBuilderInterface $chartBuilder,
        TaskCalendarRepository $taskCalendarRepository,
        TaskChartDatasetGenerator $taskChartDatasetFactory
    ) {
        $this->scheduler = $scheduler;
        $this->chartBuilder = $chartBuilder;
        $this->taskCalendarRepository = $taskCalendarRepository;
        $this->taskChartDatasetFactory = $taskChartDatasetFactory;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        /**
         * @var RequestStack $requestStack
         */
        $requestStack = $this->container->get('request_stack');

        $param = $requestStack->getCurrentRequest()->get(self::QUERY_PARAMS);

        if ($param === self::SCHEDULE_ACTION) {
            $this->scheduler->scheduleGoals();
        }

        return $this->render('admin/index.html.twig', [
            'todays_finished_tasks' => $this->taskCalendarRepository->getTodaysFinishedTasksWithGoals(),
            'todays_unfinished_tasks' => $this->taskCalendarRepository->getTodaysUnfinishedTasksWithGoals(),
            'chart_last_7days' => $this->createChartForLastTasks('- 7 days'),
            'chart_last_month' => $this->createChartForLastTasks('- 1 month'),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Let\' Plan')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);

        yield MenuItem::linkToCrud('Goals', 'fas fa-dashboard', Goal::class);

        yield MenuItem::linkToCrud('Check Tasks', 'fas fa-tasks', TaskCalendar::class)
            ->setQueryParameter('filters[Date]', 'today')
            ->setQueryParameter('filters[isDone]', '0')
        ;
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addWebpackEncoreEntry('app');
    }

    private function createChartForLastTasks(string $period): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $dataset = $this->taskChartDatasetFactory->getChartDatasetDaysBefore($period);
        $chart->setData(
            [
                'datasets' => $dataset,
            ]
        );

        return $chart;
    }
}
