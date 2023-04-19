Feature: 
  As a user of the PAR service
  I  want to be able to view/manage partnerships
  So I can comply with the BEIS standards for goods and services

  @regression @publicRegistrySearch
  Scenario: Verify a Non-registered User can Search the Public Register (Happy Path - PAR-2057)
    Given the user is on the PAR home page
    When the user is on the search for a partnership page
    Then the user can search for a PA Organisation Trading name Company number
    And the user is shown the information for that partnership

  @regression @publicRegistrySearch
  Scenario: Verify a Registered User can Search the Public Register (Happy Path - PAR-2057)
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    And the user clicks the PAR Home page link
    When the user is on the search for a partnership page
    Then the user can search for a PA Organisation Trading name Company number
    And the user is shown the information for that partnership

  # Currently this test require the new person's name changing whilst we cannot remove a contact.
  @regression @PARNewsSubscription
  Scenario: Verify a new Authority contact is subscribed to PAR News (Happy Path - PAR-2072)
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user adds a new person to the contacts successfully with the following details:
      | Title | Firstname | Lastname | WorkNumber  | MobileNumber | Email                     |
      | Dr    | Angel     | Croft    | 01204456509 |  07983012783 | par_authority@example.com |
    Then the user can update the new contact to subscribe to PAR News
    #Helpdesk Verification
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Subscriptions page
    And the user searches for the par_authority email "par_authority@example.com"
    Then the user can verify the email is successfully in the Subscriptions List

  # Currently this test require the new person's name changing whilst we cannot remove a contact to reset.
  @regression @PARNewsSubscription
  Scenario: Verify a new Authority contact is subscribed to PAR News (Happy Path - PAR-2072)
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user adds a new person to the contacts successfully with the following details:
      | Title | Firstname | Lastname | WorkNumber  | MobileNumber | Email                     |
      | Mrs   | Paula     | Main     | 01204456511 |  07165439876 | par_authority@example.com |
    Then the user can update the new contact to unsubscribe from PAR News
    #Helpdesk Verification
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Subscriptions page
    And the user searches for the par_authority email "par_authority@example.com"
    Then the user can verify the email is successfully removed from the Subscriptions List

  @regression @PARNewsSubscription
  Scenario: Verify a Helpdesk user can add an Email to the PAR News Subscription List (Happy Path - PAR-2072)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Manage a subscription list page
    And the user enters a new email to add to the list "authority@authority.co.uk"
    Then the user can verify the new email was added successfully

  @regression @PARNewsSubscription
  Scenario: Verify a Helpdesk user can remove an Email from the PAR News Subscription List (Happy Path - PAR-2072)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Manage a subscription list page
    And the user enters an email to be removed from the list "authority@authority.co.uk"
    Then the user can verify the email was removed successfully

  # Idea: add a step in the THEN method to replace to reset the Subscription list?
  @regression @PARNewsSubscription
  Scenario: Verify a Helpdesk user can replace the PAR News Subscription List with a new List (Happy Path - PAR-2072)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Manage a subscription list page
    And the user enters a list of new emails to replace the subscription list:
      | Email              |
      | user05@testing.com |
      | user06@testing.com |
      | user07@testing.com |
      | user08@testing.com |
    Then the user can verify an email from the original list was removed successfully "user01@testing.com"

  # Person's details will need to be changed each time unless there is a way to remove these people after each test run.
  @regression @helpDesk @managePeople
  Scenario: Verify the Addition of a new person as a Help Desk User (Happy Path - PAR-2098)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user creates a new person with the following details:
      | Title | Firstname | Lastname | WorkNumber | MobileNumber | Email                       |
      | Mr    | Marc      | Aureli   |       0170 |        07165 | marcus_aurelius@example.com |
    Then the user can verify the person "Marc Aureli" was created successfully

  # Update existing person
  @regression @helpDesk @managePeople
  Scenario: Verify the Update of an existing person as a Help Desk User (Happy Path - PAR-2098)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for an existing person "Marc Aureli" successfully
    And the user updates an existing person with the following details:
      | Title | Firstname | Lastname | WorkNumber  | MobileNumber | Email                       |
      | Dr    | Marcus    | Aurelius | 01204456511 |  07165439876 | marcus_aurelius@example.com |
    Then the user can verify the person "Marcus Aurelius" was updated successfully
    
    