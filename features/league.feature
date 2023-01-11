Feature: League
  In order set Priorities in System you can use League to find out which is most important to you
  As admin/user
  I can be make league constest/turnament

  Background:
    Given I am login as admin
  Scenario: Is Available League Page and can create League
    Given I am on "/admin"
    When I follow "Goal League"
    Then I should see "Create League"
  Scenario: Create new Turnament for Category God
    Given There is no Turnament in db
    When I create new Turnament for Category "God"
    Then new turnament will be created
  Scenario: Open existing Turnament for Category
    When I create new Turnament for Category "God"
    # it means i can make pairings and start first round, start next round, can choose option in league
    Then existing turnament can be opened

