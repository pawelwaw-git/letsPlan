# Feature goalmanagment plan create caterogies and required types of goals as background then crud categories and then test goals
Feature: Goal/Category management
  In order to gain access to the site management area of your goals
  In order to manage your goals 
  As an admin
  I need to be able to create,read,update,delete Categories and Goals

  Background:
    Given I am login as admin
    And There are standard types of categories
  Scenario: Creating category
    # probably it will be better to create function to describe easier
    Given I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\CategoryCrudController"
    When I follow "Add Category"
    And I fill in "Name" with "Test Category"
    And I press "Create"
    Then I should see "Test Category"
  Scenario: Updating Category
    Given I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\CategoryCrudController"
    When I follow "Edit"
    And I fill in "Category_Name" with "New Category"
    And I press "Save changes"
    Then I should see "New Category"
  Scenario: Creating Goal
    Given I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\GoalCrudController"
    When I follow "Add Goal"
    And I fill in "Name" with "Tests Goal"
    And I fill in "Priority" with "3"
    And I fill in "Type" with "simple_habbit"
    And I fill in "Category" with "1"
    And I fill in "Repeatable" with "every_day"
    And I fill in "Description" with "Sample description of Goal"
    And I press "Create"
    Then I should see "Tests Goal"
    And I should see "SimpleHabbit"
    And I should see "1#New Category"
    And I should see "Sample description of Goal"
  Scenario: udpate Goal
    Given I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\GoalCrudController"
    When I follow "Edit"
    And I fill in "Name" with "Test Goal"
    And I fill in "Priority" with "4"
    And I fill in "Type" with "task"
    And I fill in "Category" with "1"
    And I fill in "Description" with "Sample description of Goal update"
    And I press "Save changes"
    Then I should see "Test Goal"
    And I should see "4"
    And I should see "Task"
    And I should see "1#New Category"
    And I should see "Sample description of Goal update"
# commented because can set up container for behat headless correctly
#  @javascript
#  Scenario: Delete Goal
#    Given I am login as admin
#    And I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\GoalCrudController"
#    When I click Element with class "action-delete"
#    And I wait for Modal
#    And I press "Delete"
#    And I should not see "Test Goal"
#  @javascript
#  Scenario: Delete Category
#    Given I am login as admin
#    And I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\CategoryCrudController"
#    When I click Element with class "action-delete"
#    And I wait for Modal
#    And I press "Delete"
#    And I should not see "New Category"