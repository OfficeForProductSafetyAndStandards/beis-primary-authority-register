Feature: Other
  As a user of the PAR service
  I  want to be able to view/manage partnerships
  So I can comply with the BEIS standards for goods and services

  @regression @authority @authorityManagement
  Scenario: Verify Addition/Update of Authority (Happy Path - PAR-1849, PAR-1850)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user creates a new authority with the following details:
      | Authority Type | ONS Code | Regulatory Function | addressline1  | town    | postcode |
      | Council Area   | 43453465 | Cookie control      | 32 Bramtom Rd | Windsor | SL4 5PN  |
    Then the authority is created sucessfully
    #Update All Fields for newly created Authority
    When the user searches for the last created authority
    And the user updates all the fields for newly created authority
    Then the update for the authority is successful

  @regression @organisation
  Scenario: Verify Update of Organisation (Happy Path - PAR-1851)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created organisation
    And the user updates all the fields for last created organisation
    Then all the fields are updated correctly

  @regression @publicRegistrySearch
  Scenario: Verify a Non-registered User can Search the Public Register (Happy Path - PAR-2079)
    Given the user is on the PAR home page
    When the user is on the search for a partnership page
    Then the user can search for a PA Organisation Trading name Company number
    And the user is shown the information for that partnership

  @regression @publicRegistrySearch
  Scenario: Verify a Registered User can Search the Public Register (Happy Path - PAR-2079)
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    And the user clicks the PAR Home page link
    When the user is on the search for a partnership page
    Then the user can search for a PA Organisation Trading name Company number
    And the user is shown the information for that partnership

  @regression @helpDesk @PARNewsSubscription
  Scenario: Verify an Authority contact is subscribed to PAR News (Happy Path - PAR-2076)
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user selects a contact to update
    Then the user can successfully subscribe to PAR News
    #Helpdesk Verification
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Subscriptions page
    And the user searches for the par_authority email
    Then the user can verify the email is successfully in the Subscriptions List

  @regression @helpDesk @PARNewsSubscription
  Scenario: Verify an Authority contact is unsubscribed from PAR News (Happy Path - PAR-2077)
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user selects a contact to update
    Then the user can successfully unsubscribe from PAR News
    #Helpdesk Verification
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Subscriptions page
    And the user searches for the par_authority email
    Then the user can verify the email is successfully removed from the Subscriptions List

  @regression @helpDesk @PARNewsSubscription
  Scenario: Verify a Helpdesk user can add a new Email to the PAR News Subscription List (Happy Path - PAR-2073)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Manage a subscription list page
    And the user enters a new email to add to the list "new_user@authority.co.uk"
    Then the user can verify the new email was added successfully

  @regression @helpDesk @PARNewsSubscription
  Scenario: Verify a Helpdesk user can remove an eisting Email from the PAR News Subscription List (Happy Path - PAR-2074)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Manage a subscription list page
    And the user enters an email to be removed from the list "new_user@authority.co.uk"
    Then the user can verify the email was removed successfully

  @regression @helpDesk @PARNewsSubscription
  Scenario: Verify a Helpdesk user can replace the PAR News Subscription List with a new List (Happy Path - PAR-2075)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Manage a subscription list page
    And the user enters a list of new emails to replace the subscription list
    Then the user can verify an email from the original list was removed successfully

  @regression @helpDesk @managePeople
  Scenario: Verify the Addition of a new person as a Help Desk User (Happy Path - PAR-2097)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user creates a new person:
      | Title | WorkNumber | MobileNumber |
      | Mr    |      01204 |              |
    Then the user can verify the person was created successfully and can see resend an account invite

  @regression @helpDesk @managePeople
  Scenario: Verify the Update of an existing person that has not created an account as a Help Desk User (Happy Path - PAR-2097)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for an existing person successfully
    And the user updates an existing person:
      | Title | WorkNumber  | MobileNumber |
      | Dr    | 01204996501 |  07405882265 |
    Then the user can verify the person was updated successfully and can see resend an account invite
