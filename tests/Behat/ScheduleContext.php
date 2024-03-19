<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Controller\Admin\DashboardController;
use Behat\Behat\Context\Context;
use Behat\Mink\Driver\BrowserKitDriver;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\AbstractBrowser;

final class ScheduleContext implements Context
{
    public function __construct(private KernelBrowser $client)
    {
    }

    /**
     * @When I request Schedule Tasks
     */
    public function iRequestScheduleTasks(): void
    {
       $this->client->request(
            'GET',
            '/admin',
            [DashboardController::QUERY_PARAMS => DashboardController::SCHEDULE_ACTION]
        );

    }
}