<?php

namespace App\Controller;

use App\Service\Bible\ApiBible;
use App\Service\Bible\RandomBibleText;
use App\Service\GoalScheduler\GoalScheduler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(RandomBibleText $randomBibleText): Response
    {
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
            'api_text' => $randomBibleText->getRandomBibleVerse(),
        ]);
    }

    #[Route('/cron_task_schedule', name: 'task_scheduler')]
    public function taskScheduler(GoalScheduler $scheduler): Response
    {
        $scheduler->scheduleGoals();
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
