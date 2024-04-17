<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Controller\Admin\DashboardController;
use App\Entity\Admin;
use App\Entity\Category;
use App\Repository\AdminRepository;
use App\Repository\CategoryRepository;
use Behat\Behat\Context\Context;
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
    private AdminRepository $userEntity;
    private CategoryRepository $categoryEntity;
    private UserPasswordHasherInterface $passwordHarsher;
    private RouterInterface $router;

    public function __construct(
        AdminRepository $user,
        CategoryRepository $category,
        UserPasswordHasherInterface $passwordHarsher,
        RouterInterface $router
    ) {
        $this->userEntity = $user;
        $this->categoryEntity = $category;
        $this->passwordHarsher = $passwordHarsher;
        $this->router = $router;
    }

    /**
     * @Given there is an admin user :email with password :plaintextPassword
     */
    public function thereIsAnAdminUserWithPassword(string $email, string $plaintextPassword): Admin
    {
        $user = $this->userEntity->findByEmail($email);
        if (!$user) {
            $user = $this->creteNewUser($email, $plaintextPassword);
        }

        return $user;
    }

    /**
     * @Given I am login as admin
     */
    public function iAmLoginAsAdmin(): void
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
    public function thereAreStandardTypesOfCategories(): void
    {
        $categories = ['God', 'Health', 'Finance', 'Carrier', 'Hobby', 'Development'];
        foreach ($categories as $name) {
            $category = new Category();
            $category->setName($name);
            $this->categoryEntity->save($category, true);
        }
    }

    /**
     * @When I wait for Modal
     */
    public function iWaitForModal(): void
    {
        $this->getSession()->wait(
            5000,
            'document.querySelectorAll(\'#modal-delete-button\').length > 0'
        );
    }

    /**
     * @Given wait :seconds seconds
     */
    public function waitSeconds(int $seconds): void
    {
        $this->getSession()->wait($seconds * 1000);
    }

    /**
     * @When I click Element with class :cssSelectorClass
     */
    public function iClickElementWithClass(string $cssSelectorClass): void
    {
        $this->getSession()->getPage()->find('css', '.'.$cssSelectorClass)->click();
    }

    /**
     * @When I am on task_scheduler with params
     */
    public function iAmOnTaskSchedulerWithParams(): void
    {
        $link = $this->router->generate('task_scheduler', [DashboardController::QUERY_PARAMS => DashboardController::SCHEDULE_ACTION]);
        $this->visitPath($link);
    }

    private function creteNewUser(string $email, string $plaintextPassword): Admin
    {
        $user = new Admin();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $hashedPassword = $this->passwordHarsher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $this->userEntity->save($user, true);

        return $user;
    }
}
