Feature: Schedule Tasks
  In order to update our schedule with need to create specific task in calendar
  As an cron job or logged user
  I need to be able to generate schedule

  Background:
    Given I am login as admin
    And There is today "2024-02-21"

  Scenario: Create schedule by user with null date
    Given I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\GoalCrudController"
    And there is no active Goals in db
    And there are following Goals with lastDates in db:
      | goal_type   | lastDate |
      | every_day   | null     |
      | every_week  | null     |
      | every_month | null     |
      | none        | null     |
    When I follow "Schedule Tasks"
    Then I should see "Welcome"
    Then there are following planed tasks in db:
      | goal_type   | startDate | expected |
      | every_day   | null      | 60       |
      | every_week  | null      | 9        |
      | every_month | null      | 2        |
      | none        | null      | 0        |

  Scenario: Create schedule by user with 1-12-2022 date
    Given I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\GoalCrudController"
    And there is no active Goals in db
    And there are following Goals with lastDates in db:
      | goal_type   | lastDate   |
      | every_day   | 2023-12-01 |
      | every_week  | 2023-12-01 |
      | every_month | 2023-12-01 |
      | none        | 2023-12-01 |
    When I follow "Schedule Tasks"
    Then I should see "Welcome"
    And there are following planed tasks in db:
      | goal_type   | startDate  | expected |
      | every_day   | 2023-12-01 | 141      |
      | every_week  | 2023-12-01 | 20       |
      | every_month | 2023-12-01 | 4        |
      | none        | 2023-12-01 | 0        |