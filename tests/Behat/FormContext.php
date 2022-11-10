<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\Admin;
use App\Entity\Category;
use App\Repository\AdminRepository;
use App\Repository\CategoryRepository;
use App\Repository\TaskCalendarRepository;
use App\Service\GoalScheduler;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterFeatureScope;
use Behat\Behat\Hook\Scope\BeforeFeatureScope;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class FormContext extends MinkContext implements Context
{
    private $userEntity;
    private $categoryEntity;
    private $passwordHasher;
    private $tasksRepo;
    private $router;

    public function __construct(
        AdminRepository $user,
        CategoryRepository $category,
        TaskCalendarRepository $tasksRepo,
        UserPasswordHasherInterface $passwordHasher,
        RouterInterface $router
    ) {
        $this->userEntity = $user;
        $this->categoryEntity = $category;
        $this->passwordHasher = $passwordHasher;
        $this->tasksRepo = $tasksRepo;
        $this->router = $router;
    }

    /** @BeforeFeature */
    public static function setupFeature(BeforeFeatureScope $scope): void
    {
        shell_exec("php bin/console doctrine:database:create --env=test");
        shell_exec("php bin/console doctrine:schema:create --env=test");
    }

    /** @AfterFeature */
    public static function teardownFeature(AfterFeatureScope $scope): void
    {
        // shell_exec("php bin/console doctrine:database:drop --env=test --force");
    }

    /**
     * @Given there is an admin user :email with password :plaintextPassword
     */
    public function thereIsAnAdminUserWithPassword(string $email, string $plaintextPassword)
    {
        $user = $this->userEntity->findByEmail($email);
        if (!$user) {
            $user = $this->creteNewUser($email, $plaintextPassword);
        }
        return $user;
    }

    private function creteNewUser(string $email, string $plaintextPassword): Admin
    {
        $user = new Admin();
        $user->setEmail($email);
        $user->setRoles(array('ROLE_ADMIN', 'ROLE_USER'));
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $this->userEntity->save($user, true);
        return $user;
    }

    /**
     * @Given I am login as admin
     */
    public function iAmLoginAsAdmin()
    {
        $email = 'admin@example.com';
        $password = 'adminpass';
        $this->thereIsAnAdminUserWithPassword($email, $password);
        $this->visitPath('/login');
        $this->fillField('Password', $password);
        $this->fillField('Email', $email);
        $this->pressButton('Sign in');
    }

    /**
     * @Given There are standard types of categories
     */
    public function thereAreStandardTypesOfCategories()
    {
        $categories = ['God', 'Health', 'Finance', 'Carrier','Hobby','Development'];
        foreach ($categories as $name) {
            $category = new Category();
            $category->setName($name);
            $this->categoryEntity->save($category);
        }
    }

    /**
     * @When I wait for Modal
     */
    public function iWaitForModal()
    {
        $this->getSession()->wait(
            5000,
            'document.querySelectorAll(\'#modal-delete-button\').length > 0'
        );
    }

    /**
     * @Given wait :seconds seconds
     */
    public function waitSeconds($seconds)
    {
        $this->getSession()->wait($seconds * 1000);
    }

    /**
     * @When I click Element with class :cssSelectorClass
     */
    public function iClickElementWithClass($cssSelectorClass)
    {
        $this->getSession()->getPage()->find('css', "." . $cssSelectorClass)->click();
    }

    /**
     * @When I am on task_scheduler with params
     */
    public function iAmOnTaskSchedulerWithParams()
    {
        $link = $this->router->generate('task_scheduler', [GoalScheduler::QUERY_PARAMS => GoalScheduler::SCHEDULE_ACTION]);
        $this->visitPath($link);
    }
}
