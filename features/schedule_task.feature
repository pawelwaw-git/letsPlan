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
      | goal_type   | lastDate |
      | every_day   | null     |
      | every_week  | null     |
      | every_month | null     |
      | none        | null     |
    When I request Schedule Tasks
#    Then I should see "Welcome"
    Then there are following planed tasks in db:
      | goal_type   | startDate | expected |
      | every_day   | null      | 61       |
      | every_week  | null      | 9        |
      | every_month | null      | 2        |
      | none        | null      | 0        |
  Scenario: Create schedule by user with 1-12-2022 date
    Given I am on "/admin?crudAction=index&crudControllerFqcn=App\Controller\Admin\GoalCrudController"
    And there is no active Goals in db
    And there are following Goals with lastDates in db:
      | goal_type   | lastDate   |
      | every_day   | 2022-12-01 |
      | every_week  | 2022-12-01 |
      | every_month | 2022-12-01 |
      | none        | 2022-12-01 |
    When I request Schedule Tasks
    Then I should see "Welcome"
    And there are following planed tasks in db:
      | goal_type   | startDate  | expected |
      | every_day   | 2022-12-01 | 531      |
      | every_week  | 2022-12-01 | 76       |
      | every_month | 2022-12-01 | 17       |
      | none        | 2022-12-01 | 0        |

#   TODO this should be tested in other way -- to remove, change to command test not
#  Scenario: Schedule task via Cron
#    Given there is no active Goals in db
#    And there is "every_week" Goal with  lastDate "2022-12-01" in db
#    When I run schedule Goal Command
#    Then I should see "Welcome"
#    And there are following planed tasks in db:
#      | goal_type   | startDate  | expected |
#      | every_week  | 2022-12-01 | 76       |