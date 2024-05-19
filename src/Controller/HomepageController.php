<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Bible\RandomBibleText;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
}
