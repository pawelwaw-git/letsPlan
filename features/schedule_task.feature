Feature: Schedule Tasks
  In order to update our schedule with need to create specific task in calendar
  As an cron job or logged user
  I need to be able to generate schedule

  Background:
    Given I am login as admin
    And There are different goal types
  Scenario: Create schedule by user
    Given I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\GoalCrudController"
    When I follow "Schedule Tasks"
    Then I should see "Welcome"
    And there and planed tasks in db
  Scenario: Schedule task via Cron
    Given truncate tasks and reset last_date_schedule
    When I am on task_scheduler with params
    Then I should see "Welcome"
    And there and planed tasks in db