Feature: 
  As a user of the PAR service
  I  want to be able to view/manage partnerships
  So I can comply with the BEIS standards for goods and services

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
      | SIC Code            | No of Employees |	Legal Entity Name	| Legal entity Type 			| Company number	|	Business Description |
      | allow people to eat | 10 to 49        |	LE1								| unregistered  				  | 12345678				|	Test Business        |
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

  @regression @enforcement
  Scenario: Verify Send Notification of Proposed Enforcement, Approval and Removal (Happy Path - PAR-1852, PAR-1853, PAR-1854)
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

  @regression @inspectionplan @inspectionfeedback @deviationrequest
  Scenario: Verify Upload of Inspection Plan (Happy Path - PAR-1856)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user uploads an inspection plan against the partnership with the following details:
      | Title              | Description |
      | INspection Title 1 | Test 1      |
      
  @regression @inspectionplan @inspectionfeedback
  Scenario: Verify Update of Inspection Plan (Happy Path - PAR-1865)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user updates the last created inspection plan against the partnership with the following details:
      | Title              | Description |
      | Inspection Title 2 | Test 2      |
    Then the inspection plan is updated correctly

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
    
  @regression @inspectionplan
  Scenario: Verify Revocation and Removal of Inspection Plan (Happy Path - PAR-1866, PAR-1867)
   Given the user is on the PAR login page
   And the user logs in with the "par_helpdesk@example.com" user credentials
   When the user searches for the last created partnership
   Then the user successfully revokes the last created inspection plan
   
   #remove the inspection plan
   When the user revokes the last created inspection plan
   Then the inspection plan is successfully removed
   
   

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

  @regression @enforcement
  Scenario: Verify a Help Desk user can view Enforcement Notice details (Happy Path - PAR-2099, PAR-1855)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user selects the last created enforcement notice
    Then the user can verify the enforcement officers details are displayed
    #Remove the Enforcement Notice
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created enforcement notice
    Then the user removes the enforcement notice successfully

  @regression @enquiry
  Scenario: Verify a Help Desk user can view general enquiry details (Happy Path - PAR-2100)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    Then the user submits a general enquiry with the following details:
      | Description                  |
      | Enforcement Officer Enquiry. |
      
    # View the last created general enquiry as a Help Desk user.
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created general enquiry
    Then the user successfully views the enquiry
