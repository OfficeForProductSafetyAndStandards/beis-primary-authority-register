Feature: General
  As a user of the PAR service
  I  want to be able to view/manage partnerships
  So I can comply with the BEIS standards for goods and services

  @regression @partnershipapplication @direct @update @usermanagement @organisation @enforcement @inspectionplan @inspectionfeedback @deviationrequest @enquiry @advicenotice
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
      | SIC Code            | No of Employees | Legal Entity Name | Legal entity Type | Company number | Business Description |
      | allow people to eat | 10 to 49        | LE1               | unregistered      |       12345678 | Test Business        |
    Then the second part of the partnership application is successfully completed
    #verify update of newly created partnership
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created partnership
    And the user updates the partnership information with the following info: "Updated Partnership info"
    Then the partnership is updated correctly

  @regression @usermanagement @login @enforcement @inspectionplan @inspectionfeedback @deviationrequest @enquiry @advicenotice @direct
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

  @regression @direct @partnershipapplication @update
  Scenario: Update all Partnership details successfully (Happy Path - PAR-2214)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership Authority
    And the user updates the About the Partnership and Regulatory Functions:
      | About the Partnership |
      | Updated Partnership   |
    Then the About the Partnership and Regulatory Functions are updated Successfully
    # Change Partnership details page to updated the rest of the Partnership
    When the user searches for the last created partnership Organisation
    And the user updates the Partnerships details with the following:
      | Address1    | Address2 | Town | County             | Country | Nation Value | Post Code | About the Organisation | SIC Code          | Trading Name |
      | 01 new road | Market   | Bury | Greater Manchester | GB      | GB-SCT       | BL2 4BD   | Updated Info           | you sell cookies. | Name Update  |
    Then all of the Partnership details have been updated successfully

  # Update Legal Entities, Primary Authority Contact and Organisation Contact Tests go here.
  @regression @inspectionplan @inspectionfeedback @deviationrequest
  Scenario: Verify Upload of Inspection Plan (Happy Path - PAR-1856)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user uploads an inspection plan against the partnership with the following details:
      | Title              | Description |
      | Inspection Title 1 | Test 1      |

  @regression @inspectionplan @inspectionfeedback
  Scenario: Verify Update of Inspection Plan (Happy Path - PAR-1865)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user updates the last created inspection plan against the partnership with the following details:
      | Title              | Description |
      | Inspection Title 2 | Test 2      |
    Then the inspection plan is updated correctly

  @regression @advicenotice
  Scenario: Verify Upload of Advice Notice (Happy Path - PAR-1873)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user uploads an advice notice against the partnership with the following details:
      | Title          | Type of Advice         | Reg Function   | Description |
      | Advice Title 1 | Background information | Cookie control | Test Advice |

  # Update and Archive Advice Test goes here.
  # Upload and Remove Advice Test goes here.
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

  # Create an Enforcement Notice and Block Enforcement Notice Test goes here.
  @regression @enforcement
  Scenario: Verify a Help Desk user can remove an Enforcement Notice Successfully (Happy Path - PAR-1855)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created enforcement notice
    Then the user removes the enforcement notice successfully

  # Discuss an Enforcement Notice Test goes here.
  # Create a Deviation Request and Block the Deviation Request Test goes here.
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

  # Add the Help Desk Response to the Deviation Request.
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

  # Add the Help Desk Response to Inspection Feedback.
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

  # Add the Help Desk Response to General Enquiry.
  @regression @inspectionplan
  Scenario: Verify Revocation and then Removal of an Inspection Plan (Happy Path - PAR-1866, PAR-1867)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    Then the user successfully revokes the last created inspection plan
    #remove the inspection plan
    When the user has revoked the last created inspection plan
    Then the inspection plan is successfully removed

  # Remove a Partnership Test goes here.
  @regression @partnershipapplication @coordinated
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
      | SIC Code            | Member List Size | Business Description | Legal Entity Name | Legal entity Type | Company number |
      | allow people to eat | Medium           | Test Business        | LE1               | unregistered      |       12345678 |
    Then the second part of the partnership application is successfully completed
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created partnership

  # Nominate the Co-ordinated Partnership Test goes here. (This step is imprtant for other tests such as searching the Puplic registry, PAR-2079)
  @regression @partnershipapplication @coordinated
  Scenario: Successfully Nominate a Coordinated Partnership (Happy Path - PAR-2261)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user approves the partnership
    And the user searches again for the last created partnership
    Then the partnership is displayed with Status "Active" and Actions "Revoke partnership"
    
  # Add, Update and Cease a Member Test goes here.
  # Upload a Members list Test goes here.
  # Change the Members list type Test goes here.
  @regression @usermanagement
  Scenario: Verify Completion of User Creation journey (Happy Path - PAR-1904)
    Given the user is on the PAR login page
    And the user logs in with the "par_admin@example.com" user credentials
    When the user visits the maillog page and extracts the invite link
    And the user is on the PAR login page
    And the user follows the invitation link
    And the user completes the user creation journey
    Then the user journey creation is successful
