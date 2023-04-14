Feature: 
  As a user of the PAR service
  I  want to be able to view/manage partnerships
  So I can comply with the BEIS standards for goods and services

  @regression @partnershipapplication @direct @update @usermanagement @organisation @enforcement @inspectionplan @inspectionfeedback
  Scenario: Verify Direct Partnership application by authority and completion by new business (Happy Path - PAR-1826, PAR-1835, PAR-1836, PAR-1837, PAR-1845)
    Given the user is on the PAR home page
    And the user visits the login page
    And the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    When the user creates a new "Direct" partnership application with the following details:
      | Authority | Partnership Info      | firstname  | lastname  | phone   | addressline1  | town    | postcode |
      | Lower     | Test partnership info | Test First | test last | 9797878 | 32 Bramtom Rd | Windsor | SL4 5PN  |
    Then the first part of the partnership application is successfully completed
    #second part of partnership application
    When the user searches for the last created partnership
    And the user completes the partnership application with the following details:
      | SIC Code            | No of Employees | Legal entity Type | Business Description |
      | allow people to eat | 10 to 49        | Limited Company   | Test Business        |
    Then the second part of the partnership application is successfully completed
    #verify update of newly created partnership
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created partnership
    And the user updates the partnership information with the following info: "Updated Partnership info"
    Then the partnership is updated correctly

  @partnershipapplication @coordinated
  Scenario: Verify Coordinated Partnership application by authority and completion by new business (Happy Path - PAR-1838, PAR-1839, PAR-1840, PAR-1841)
    Given the user is on the PAR home page
    And the user visits the login page
    And the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    When the user creates a new "Co-ordinated" partnership application with the following details:
      | Authority | Partnership Info      | firstname  | lastname  | phone   | addressline1  | town    | postcode |
      | Upper     | Test partnership info | Test First | test last | 9797878 | 32 Bramtom Rd | Windsor | SL4 5PN  |
    Then the first part of the partnership application is successfully completed
    #second part of partnership application
    When the user searches for the last created partnership
    And the user completes the partnership application with the following details:
      | SIC Code            | Member List Size | Legal entity Type | Business Description |
      | allow people to eat | Medium           | Limited Company   | Test Business        |
    Then the second part of the partnership application is successfully completed
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created partnership

  @regression @usermanagement
  Scenario: Verify Completion of User Creation journey (Happy Path - PAR-1904)
    Given the user is on the PAR login page
    And the user logs in with the "par_admin@example.com" user credentials
    When the user visits the maillog page and extracts the invite link
    And the user is on the PAR login page
    And the user follows the invitation link
    And the user completes the user creation journey
    Then the user journey creation is successful

  @regression @usermanagement @login @enforcement @inspectionplan @inspectionfeedback
  Scenario: Verify Approval, Revokation and Restoration of Partnership journey (Happy Path - PAR-1846, PAR-1847, PAR-1848)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user approves the partnership
    And the user searches again for the last created partnership
    Then the partnership is displayed with Status "Active" and Actions "Revoke partnership"
    When the user revokes the partnership
    And the user searches again for the last created partnership
    Then the partnership is displayed with Status "Revoked" and Actions "Restore partnership"
    When the user restores the partnership
    And the user searches again for the last created partnership
    Then the partnership is displayed with Status "Active" and Actions "Revoke partnership"

  @regression @authority
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

  @regression @enforcement
  Scenario: Verify Send Notification of Proposed Enforcement, Approval and Removal (Happy Path - PAR-1852, PAR-1853, PAR-1854, PAR-1855)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user creates an enforcement notice against the partnership with the following details:
      | Enforcement Action | Title               | Regulatory Function | Description      | Attachment |
      | Proposed           | Enforcement Title 1 | Cookie control      | Enforcement desc | link.txt   |
    Then all the fields for the enforcement notice are updated correctly
    #Approve the Enforcement Notice
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user selects the last created enforcement notice
    And the user approves the enforcement notice
    Then the enforcement notice is set to approved status
    #Remove the Enforcement Notice
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created enforcement notice
    Then the user removes the enforcement notice successfully

  @regression @inspectionplan @inspectionfeedback
  Scenario: Verify Upload of Inspection Plan (Happy Path - PAR-1856)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user uploads an inspection plan against the partnership with the following details:
      | Title              | Description |
      | INspection Title 1 | Test 1      |

  @regression @inspectionfeedback
  Scenario: Verify User can Submit feedback following an Inspection plan (Happy Path - PAR-1858, PAR-1860)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user submits an inspection feedback against the inspection plan with the following details:
      | Description |
      | Test 1      |
    #Re-login as primary authority and check and approve inspection feedback
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created inspection feedback
    Then the user successfully approves the inspection feedback
    #submit response to inspection feedback
    Given the user submits a response to the inspection feedback with the following details:
      | Description   |
      | Test Response |
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created inspection feedback
    And the user sends a reply to the inspection feedback message with the following details:
      | Description |
      | Test Reply  |
    #login as authority and check message received correctly
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created inspection feedback
    Then the message is received successfully

  @regression @partnershipapplication @direct @update @usermanagement @organisation @enforcement @inspectionplan @inspectionfeedback @deviationrequest @enquiry
  Scenario: Verify Direct Partnership application by authority and completion by new business (Happy Path - PAR-1826, PAR-1835, PAR-1836, PAR-1837, PAR-1845)
    Given the user is on the PAR home page
    And the user visits the login page
    And the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    When the user creates a new "Direct" partnership application with the following details:
      | Authority | Partnership Info      | firstname  | lastname  | phone   | addressline1  | town    | postcode |
      | Lower     | Test partnership info | Test First | test last | 9797878 | 32 Bramtom Rd | Windsor | SL4 5PN  |
    Then the first part of the partnership application is successfully completed
    #second part of partnership application
    When the user searches for the last created partnership
    And the user completes the partnership application with the following details:
      | SIC Code            | No of Employees | Legal entity Type | Business Description |
      | allow people to eat | 10 to 49        | Limited Company   | Test Business        |
    Then the second part of the partnership application is successfully completed
    #verify update of newly created partnership
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created partnership
    And the user updates the partnership information with the following info: "Updated Partnership info"
    Then the partnership is updated correctly

  @partnershipapplication @coordinated
  Scenario: Verify Coordinated Partnership application by authority and completion by new business (Happy Path - PAR-1838, PAR-1839, PAR-1840, PAR-1841)
    Given the user is on the PAR home page
    And the user visits the login page
    And the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    When the user creates a new "Co-ordinated" partnership application with the following details:
      | Authority | Partnership Info      | firstname  | lastname  | phone   | addressline1  | town    | postcode |
      | Upper     | Test partnership info | Test First | test last | 9797878 | 32 Bramtom Rd | Windsor | SL4 5PN  |
    Then the first part of the partnership application is successfully completed
    #second part of partnership application
    When the user searches for the last created partnership
    And the user completes the partnership application with the following details:
      | SIC Code            | Member List Size | Legal entity Type | Business Description |
      | allow people to eat | Medium           | Limited Company   | Test Business        |
    Then the second part of the partnership application is successfully completed
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created partnership

  @regression @usermanagement
  Scenario: Verify Completion of User Creation journey (Happy Path - PAR-1904)
    Given the user is on the PAR login page
    And the user logs in with the "par_admin@example.com" user credentials
    When the user visits the maillog page and extracts the invite link
    And the user is on the PAR login page
    And the user follows the invitation link
    And the user completes the user creation journey
    Then the user journey creation is successful

  @regression @usermanagement @login @enforcement @inspectionplan @inspectionfeedback @deviationrequest @enquiry
  Scenario: Verify Approval, Revokation and Restoration of Partnership journey (Happy Path - PAR-1846, PAR-1847, PAR-1848)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user approves the partnership
    And the user searches again for the last created partnership
    Then the partnership is displayed with Status "Active" and Actions "Revoke partnership"
    When the user revokes the partnership
    And the user searches again for the last created partnership
    Then the partnership is displayed with Status "Revoked" and Actions "Restore partnership"
    When the user restores the partnership
    And the user searches again for the last created partnership
    Then the partnership is displayed with Status "Active" and Actions "Revoke partnership"

  @regression @authority
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

  @regression @enforcement
  Scenario: Verify Send Notification of Proposed Enforcement, Approval and Removal (Happy Path - PAR-1852, PAR-1853, PAR-1854, PAR-1855)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user creates an enforcement notice against the partnership with the following details:
      | Enforcement Action | Title               | Regulatory Function | Description      | Attachment |
      | Proposed           | Enforcement Title 1 | Cookie control      | Enforcement desc | link.txt   |
    Then all the fields for the enforcement notice are updated correctly
    #Approve the Enforcement Notice
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user selects the last created enforcement notice
    And the user approves the enforcement notice
    Then the enforcement notice is set to approved status
    #Remove the Enforcement Notice
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created enforcement notice
    Then the user removes the enforcement notice successfully

  @regression @inspectionplan @inspectionfeedback @deviationrequest
  Scenario: Verify Upload of Inspection Plan (Happy Path - PAR-1856)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user uploads an inspection plan against the partnership with the following details:
      | Title              | Description |
      | INspection Title 1 | Test 1      |

  @regression @inspectionfeedback
  Scenario: Verify User can Submit feedback following an Inspection plan (Happy Path - PAR-1858, PAR-1860)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user submits an inspection feedback against the inspection plan with the following details:
      | Description |
      | Test 1      |
    #Re-login as primary authority and check and approve inspection feedback
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created inspection feedback
    Then the user successfully approves the inspection feedback
    #submit response to inspection feedback
    Given the user submits a response to the inspection feedback with the following details:
      | Description   |
      | Test Response |
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created inspection feedback
    And the user sends a reply to the inspection feedback message with the following details:
      | Description |
      | Test Reply  |
    #login as authority and check message received correctly
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created inspection feedback
    Then the inspection feedback reply is received successfully

  @regression @deviationrequest
  Scenario: Verify Submission of Deviation request following an Inspection plan (Happy Path - PAR-1857, PAR-1859)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user submits a deviation request against an inspection plan with the following details:
      | Description |
      | Test 1      |
    #re-login as primary authority and check and approve deviation request
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created deviation request
    Then the user successfully approves the deviation request
    #submit response to deviation request
    Given the user submits a response to the deviation request with the following details:
      | Description   |
      | Test Response |
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created deviation request
    And the user sends a reply to the deviation request message with the following details:
      | Description |
      | Test Reply  |
    #login as authority and check message received correctly
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created deviation request
    Then the deviation reply received successfully

  @regression @enquiry
  Scenario: Verify User can Submit a general enquiry (Happy Path - PAR-1861)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user submits a general enquiry with the following details:
      | Description  |
      | Test Enquiry |
    #Re-login as primary authority and check the enquiry
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created general enquiry
    Then the user successfully views the enquiry
    #submit response to the general enquiry
    Given the user submits a response to the general enquiry with the following details:
      | Description   |
      | Test Response |
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created general enquiry
    And the user sends a reply to the general enquiry with the following details:
      | Description |
      | Test Reply  |
    #login as authority and check message received correctly
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created general enquiry
    Then the user successfully views the enquiry

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
  Scenario: Verify a Helpdesk user can add an Email to the PAR News Subscription List
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Manage a subscription list page
    And the user enters a new email to add to the list "authority@authority.co.uk"
    Then the user can verify the new email was added successfully

  @regression @PARNewsSubscription
  Scenario: Verify a Helpdesk user can remove an Email from the PAR News Subscription List
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user is on the Manage a subscription list page
    And the user enters an email to be removed from the list "authority@authority.co.uk"
    Then the user can verify the email was removed successfully

  # Idea: add a step in the THEN method to replace to reset the Subscription list?
  @regression @PARNewsSubscription
  Scenario: Verify a Helpdesk user can replace the PAR News Subscription List with a new List
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
  Scenario: Verify the Addition of a new person as a Help Desk User
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user creates a new person with the following details:
      | Title | Firstname | Lastname | WorkNumber | MobileNumber | Email                       |
      | Mr    | Marc      | Aureli   |       0170 |        07165 | marcus_aurelius@example.com |
    Then the user can verify the person "Marc Aureli" was created successfully

  # Update existing person
  @regression @helpDesk @managePeople
  Scenario: Verify the Update of an existing person as a Help Desk User
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for an existing person "Marc Aureli" successfully
    And the user updates an existing person with the following details:
      | Title | Firstname | Lastname | WorkNumber  | MobileNumber | Email                       |
      | Dr    | Marcus    | Aurelius | 01204456511 |  07165439876 | marcus_aurelius@example.com |
    Then the user can verify the person "Marcus Aurelius" was updated successfully

  @regression @enquiry
  Scenario: Verify the viewing of an Enforcement Notice and Enforcement Officers details
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for an enforcement notice "Test Business 6868" Organisation
    And clicks the Title of Action "enforcement title 1" Link
    Then the user can verify the enforcement officers details:
      | Officer                                                         | Enforcing              | Organisation       | Primary                         |
      | Grover Muppet, 01723456789, par_enforcement_officer@example.com | City Enforcement Squad | Test Business 6868 | Lower East Side Borough Council |
