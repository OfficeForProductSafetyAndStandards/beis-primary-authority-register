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
  Scenario: Verify a user receives Error Messages for required fields during the Coordinated Partnership Application and Completion (Sad Path - PAR-2459, PAR-2460)
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
    # Partnership Application Completion.
    When the user searches for the last created partnership
    And the user does not confirm they have permission from the organisation
    Then the user is shown the "Please confirm that you have been given permission." error message
    When the user confirms they have permission from the organisation
    And the user leaves the details about the organisation field empty
    Then the user is shown the "You must enter a description for the business." error message
    When the user enters details about the organisation
    And the user leaves all address details fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                  |
      | You must enter the first line of your address |
      | Town/City field is required.                  |
      | You must enter a valid postcode.              |
    When the user confirms the address details with the following:
      | AddressLine1 | AddressLine2 | Town       | County              | Country        | Nation  | Postcode |
      | 07 Bridge    | Town Hall    | Manchester | Greater Manachester | United Kingdom | England | BL2 6GH  |
    And the user leaves all contact details fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                           |
      | You must enter the first name for this contact.        |
      | You must enter the last name for this contact.         |
      | You must enter the work phone number for this contact. |
      | You must enter the email address for this contact.     |
    When the user confirms the primary contact details with the following:
      | Title | Firstname | Lastname | WorkNumber  | MobileNumber | Email                  | ContactNote               |
      | Dr    | Jerry     | Mouse    | 02045678912 |  07745665913 | jerrymouse@example.com | Coordinated Contact Note. |
    And the user confirms the sic code
    And the user confirms the size of the membership list as "Medium"
    And the user leaves the trading name field empty
    Then the user is shown the "You must enter the trading name for this organisation." error message
    When the user enters a trading name "Error Messages Co."
    And the user does not select a registered, charity or unregistered legal entity
    Then the user is shown the "Please choose whether this is a registered or unregistered legal entity." error message
    When the user selects an "unregistered" legal entity
    And the user does not select a legal entity type or enter a legal entity name
    Then the user is shown the following error messages:
      | ErrorMessage                                            |
      | You must choose which legal entity type you are adding. |
      | Please enter the name of the legal entity.              |
    When the user chooses a legal entity with the following details:
      | Legal Entity Name  | Legal Entity Type |
      | Error Messages Co. | Partnership       |
    And the user confirms the legal entity
    And the user does not confirm they have read the terms and conditions
    Then the user is shown the "Please confirm you have read the terms and conditions." error message
    And the user confirms the second part of the partnership application
    And the user signs out
    
	@regression @sadpath @partnershipapplication
  Scenario: Verify a user receives Error Messages for required fields when Nominating a Coordinated Partnership (Sad Path - PAR-2461)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "secretary_state@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the approve partnership action link
    And the user does not confirm they are authorised to approve
    Then the user is shown the "You must confirm you are authorised to approve this partnership." error message
    When the user confirms they are authorised to approve
    And the user selects the bespoke Radio but not the type of bespoke regulatory functions
    Then the user is shown the "You must choose at least one regulatory function." error message
    When the user selects the type of bespoke regulatory functions
    And the user searches again for the last created partnership
    Then the partnership is approved successfully
    And the user signs out