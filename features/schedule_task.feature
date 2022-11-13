Feature: Schedule Tasks
  In order to update our schedule with need to create specific task in calendar
  As an cron job or logged user
  I need to be able to generate schedule

  Background:
    Given I am login as admin
  Scenario: Create schedule by user with null date
    Given I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\GoalCrudController"
    And there is no active Goals in db
    And there are following Goals with lastDates in db:
      | goal_type  | lastDate |
      | every_day  | null |
      | every_week  | null |
      | every_month  | null |
      | none  | null |
    When I follow "Schedule Tasks"
    Then I should see "Welcome"
    And there are following planed tasks in db:
      | goal_type  | startDate |
      | every_day  | null |
      | every_week  | null |
      | every_month  | null |
      | none  | null |
  Scenario: Create schedule by user with 1-12-2022 date
    Given I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\GoalCrudController"
    And there is no active Goals in db
    And there are following Goals with lastDates in db:
      | goal_type  | lastDate |
      | every_day  | 2022-12-01 |
      | every_week  | 2022-12-01 |
      | every_month  | 2022-12-01 |
      | none  | 2022-12-01 |
    When I follow "Schedule Tasks"
    Then I should see "Welcome"
    And there are following planed tasks in db:
      | goal_type  | startDate |
      | every_day  | 2022-12-01 |
      | every_week  | 2022-12-01 |
      | every_month  | 2022-12-01 |
      | none  | 2022-12-01 |
  Scenario: Schedule task via Cron
    Given there is no active Goals in db
    And there is "every_week" Goal with  lastDate "2022-12-01" in db
    When I am on task_scheduler with params
    Then I should see "Welcome"
    And there are planed "every_week" tasks in db from date "2022-12-01"