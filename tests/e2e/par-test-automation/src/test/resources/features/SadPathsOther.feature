#Author: your.email@your.domain.com
#Keywords Summary :
#Feature: List of scenarios.
#Scenario: Business rule through list of steps with arguments.
#Given: Some precondition step
#When: Some key actions
#Then: To observe outcomes or validation
#And,But: To enumerate more Given,When,Then steps
#Scenario Outline: List of steps for data-driven as an Examples and <placeholder>
#Examples: Container for s table
#Background: List of steps run before each of the scenarios
#""" (Doc Strings)
#| (Data Tables)
#@ (Tags/Labels):To group Scenarios
#<> (placeholder)
#""
## (Comments)
Feature: Other Sad Paths

  @regression @sadpath @invalidsignin @sadPathOther
  Scenario Outline: Verify Unauthorised user credentials receive an errormessage when attempting to sign in (Sad Path - PAR-2385)
    Given the user is on the home page
    And the user is on the sign in page
    When the user enters the following <email> and <password> credentials
    Then the user is shown an error message <errormessage1> <errormessage2> successfully

    Examples:
    	| email                       | password     | errormessage1                                            | errormessage2                                            |
      |                             |              | The Enter your e-mail address is required.               | The Enter your password is required.                     |
      |                             | TestPassword | The Enter your e-mail address is required.               |                                                          |
      | par_coordinator@example.com |              | The Enter your password is required.                     | Unrecognized username or password. Forgot your password? |
      | par_coordinator@example.com | Invalid      | Unrecognized username or password. Forgot your password? |                                                          |
      | Invalid                     | TestPassword | Unrecognized username or password. Forgot your password? |                                                          |
      | Invalid                     | Invalid      | Unrecognized username or password. Forgot your password? |                                                          |
