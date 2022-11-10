Feature: Authentication
  In order to gain access to the site management area of your goals
  As an admin
  I need to be able to login and logout

  Background:
    Given there is an admin user "admin@example.com" with password "adminpass"
  Scenario: Logging in
    Given I am on "/"
    When I follow "Login"
    And I fill in "Email" with "admin@example.com"
    And I fill in "Password" with "adminpass"
    And I press "Sign in"
    #And print last response
    #And I wait 2 seconds
    Then I should see "admin@example.com"
  Scenario: Logging Invalid credentials
    Given I am on "/"
    When I follow "Login"
    And I fill in "Email" with "admin1@example.com"
    And I fill in "Password" with "adminpass"
    And I press "Sign in"
    Then I should see "Invalid credentials"
  Scenario: Can Log out
    Given I am login as admin
    When I follow "Sign out"
    Then I should see "Please sign in"