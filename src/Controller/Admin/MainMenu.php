<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Goal;
use App\Entity\TaskCalendar;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

class MainMenu
{
    public static function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Goals', 'fas fa-dashboard', Goal::class);
        yield MenuItem::linkToCrud('Check Tasks', 'fas fa-tasks', TaskCalendar::class)
            ->setQueryParameter('filters[Date]', 'today')
            ->setQueryParameter('filters[isDone]', '0');
        yield MenuItem::linkToRoute('Goal League', 'fa fa-trophy', 'admin_league');
    }
}