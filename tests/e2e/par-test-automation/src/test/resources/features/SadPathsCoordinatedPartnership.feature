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
#Sample Feature Definition Template
Feature: Coordinated Partnership Sad Paths

  @regression @sadpath @partnershipapplication
  Scenario: Verify a user receives Error Messages for required fields during the Coordinated Partnership Application (Sad Path - PAR-2459)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    When the user applies for a new partnership
    And does not select a primary authority
    Then the user is shown the "You must select an authority." error message
    When the user selects a primary authority
    And the user does not select a partnership type
    Then the user is shown the "Please select the type of application." error message
    When the user selects a "Co-ordinated" partnership type
    And the user does not confirm the terms and conditions
    Then the user is shown the "Please confirm that all conditions for a new partnership have been met." error message
    When the user confirms the partnership terms and conditions
    And the user leaves the information about the partnership field empty
    Then the user is shown the "You must enter some information about this partnership." error message
    When the user enters informations about the partnership
    And the user leaves the organisation name field empty
    Then the user is shown the "You must enter the organisation's name." error message
    When the user enters an orgnasiation name
    And the user leaves all the address fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                  |
      | You must enter the first line of your address |
      | Town/City field is required.                  |
      | You must enter a valid postcode.              |
    When the user enters an address with the following details:
      | AddressLine1 | AddressLine2 | Town       | County              | Country        | Nation  | Postcode |
      | 07 Bridge    | Town Hall    | Manchester | Greater Manachester | United Kingdom | England | BL2 6GH  |
    And the user leaves the contact details fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                           |
      | You must enter the first name for this contact.        |
      | You must enter the last name for this contact.         |
      | You must enter the work phone number for this contact. |
      | You must enter the email address for this contact.     |
    When the user enters a contact with the following details:
      | Title | Firstname | Lastname | WorkNumber  | MobileNumber | Email                  |
      | Dr    | Jerry     | Mouse    | 02045678912 |  07745665913 | jerrymouse@example.com |
    And the user invites the business
    And the user clicks the save button without accepting the terms and conditions
    Then the user is shown the "Please confirm you have read the terms and conditions." error message
    When the user accepts the partnership terms and conditions
    Then the user confirms the first part of the partnership application
