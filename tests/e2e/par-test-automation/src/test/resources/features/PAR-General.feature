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

  @regression @usermanagement @login @enforcement @inspectionplan @inspectionfeedback @deviationrequest @enquiry @advicenotice @direct @update
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

  # Update Legal Entities test here.
  @regression @direct @update
  Scenario: Add, Update and Remove a Primary Authority Contact for a Partnership with a User Account Invite Successfully (Happy Path - PAR-2242)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership Authority
    And the user adds a Primary Authority contact to be Invited with the following details:
      | Title | WorkNumber  | MobileNumber | ContactNotes       |
      | Mr    | 01706553019 |  07651044910 | Test contact note. |
    Then the new Primary Authority contact is added Successfully
    # Update the new contact
    When the user updates the new Primary Authority contact with the following details:
      | Title | WorkNumber  | MobileNumber | ContactNotes              |
      | Dr    | 01706553019 |  07356001870 | Test contact note update. |
    Then the new Primary Authority contact is updated Successfully
    # Remove the new contact
    When the user removes the new Primary Authority contact
    Then the new Primary Authority contact is removed Successfully

  @regression @direct @update
  Scenario: Add, Update and Remove an Organisation Contact for a Partnership with a User Account Invite Successfully (Happy Path - PAR-2244)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership Authority
    And the user adds a new Organisation contact to be Invited with the following details:
      | Title | WorkNumber  | MobileNumber | ContactNotes       |
      | Mrs   | 01755892240 |  07651044912 | Test contact note. |
    Then the new Organisation contact is added Successfully
    # Update the new contact
    When the user updates the new Organisation contact with the following details:
      | Title | WorkNumber  | MobileNumber | ContactNotes              |
      | Dr    | 01706553019 |  07356001143 | Test contact note update. |
    Then the new Organisation contact is updated Successfully
    # Remove the new contact
    When the user removes the new Organisation contact
    Then the new Organisation contact is removed Successfully

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
  Scenario: Verify Upload, Update and Archive of an Advice Notice (Happy Path - PAR-1874, PAR-1875)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user uploads an advice notice against the partnership with the following details:
      | Title              | Type of Advice         | Reg Function   | Description         |
      | Partnership Advice | Background information | Cookie control | Advice description. |
    Then the advice notice it uploaded successfully and set to active
    When the user selects the edit advice action link
    And the user edits the advice notice with the following details:
      | Title                     | Type of Advice                                | Description                |
      | Partnership Advice Update | Primary Authority advice for the organisation | Advice description update. |
    Then the advice notice it updated successfully
    When the user archives the advice notice with the following reason "Advice Complete."
    Then the advice notice is archived successfully

  @regression @advicenotice
  Scenario: Verify Upload and Removal of an Advice Notice (Happy Path - PAR-1876)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user uploads an advice notice against the partnership with the following details:
      | Title              | Type of Advice         | Reg Function   | Description         |
      | Notice for Removal | Background information | Cookie control | Advice description. |
    Then the advice notice it uploaded successfully and set to active
    When the user removes the advice notice with the following reason "Advice no long needed."
    Then the advice notice is removed successfully

  @regression @advicenotice
  Scenario: Verify Upload of Advice Notice (Happy Path - PAR-1873)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user uploads an advice notice against the partnership with the following details:
      | Title          | Type of Advice         | Reg Function   | Description |
      | Advice Title 1 | Background information | Cookie control | Test Advice |
    Then the advice notice it uploaded successfully and set to active

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

  @regression @enforcement
  Scenario: Verify a Help Desk user can remove an Enforcement Notice Successfully (Happy Path - PAR-1855)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created enforcement notice
    Then the user removes the enforcement notice successfully

  @regression @enforcement
  Scenario: Verify an Enforcement Notice can be Blocked (Happy Path - PAR-1970)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user creates an enforcement notice against the partnership with the following details:
      | Enforcement Action | Title                            | Regulatory Function | Description       | Attachment |
      | Proposed           | Enforcement Notice to be Blocked | Cookie control      | Test Enforcement. | link.txt   |
    Then all the fields for the enforcement notice are updated correctly
    #Block the Enforcement Notice
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user selects the last created enforcement notice
    And the user blocks the enforcement notice with the following reason: "Test Block"
    Then the enforcement notice is set to blocked status

  @regression @enforcement @enquiry
  Scenario: Verify the Discussion of an Enforcement Notice (Happy Path - PAR-2272)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user sends a general enquiry for an enforcement notice with the following details:
      | Description            |
      | Enforcement Discussion |
    Then the Enquiry is created Successfully
    #Re-login as primary authority and check the enquiry
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created general enquiry
    Then the user successfully views the enquiry

  @regression @deviationrequest
  Scenario: Verify a Deviation Request can be Blocked (Happy Path - PAR-2275)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user submits a deviation request against an inspection plan with the following details:
      | Description                     |
      | Deviation Request to be Blocked |
    Then the Deviation Request is created Successfully
    #Re-login as primary authority and check thedeviation request
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created deviation request
    And the user blocks the deviation request with the following reason: "Test Block"
    Then the deviation request is set to blocked status

  @regression @deviationrequest
  Scenario: Verify Submission of Deviation request following an Inspection plan (Happy Path - PAR-1857, PAR-1859)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user submits a deviation request against an inspection plan with the following details:
      | Description |
      | Test 1      |
    Then the Deviation Request is created Successfully
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
    Then the Enquiry is created Successfully
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

  @regression @usermanagement
  Scenario: Verify Completion of User Creation journey (Happy Path - PAR-1904)
    Given the user is on the PAR login page
    And the user logs in with the "par_admin@example.com" user credentials
    When the user visits the maillog page and extracts the invite link
    And the user is on the PAR login page
    And the user follows the invitation link
    And the user completes the user creation journey
    Then the user journey creation is successful

	@regression @direct @deletePartnership
  Scenario: Verify a Nominated Direct Partnership can be Deleted Successfully (Happy Path - PAR-2277)
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
		# Nominate the Partnership
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user approves the partnership
    And the user searches again for the last created partnership
    Then the partnership is displayed with Status "Active" and Actions "Revoke partnership"
		# Delete the Partnership
    When the user Deletes the Partnership with the following reason: "Partnership is incorrect."
    Then the Partnership was Deleted Successfully
  
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

  @regression @partnershipapplication @coordinated @organisationMember
  Scenario: Successfully Nominate a Coordinated Partnership (Happy Path - PAR-2261)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user approves the partnership
    And the user searches again for the last created partnership
    Then the partnership is displayed with Status "Active" and Actions "Revoke partnership"

  # Add, Update and Cease a Member Test goes here.
  @regression @coordinated @organisationMember
  Scenario: Verify Addition of a Single Member Organisation to a Coordinated partnership (Happy Path - PAR-1868)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user adds a single member organisation to the patnership with the following details:
      | Organisation Name    | Address Line 1 | Address Line 2 | Town City  | County             | Postcode | Title | WorkNumber  | MobileNumber | Legal Entity Type | Legal Entity Name |
      | Testing Organisation | 02 New Street  | Market Hall    | Manchester | Greater Manchester | BL2 4BL  | Dr    | 02345678901 |  07890123456 | Sole trader       | Testing Co.       |
    Then the user member organistion has been added to the partnership successfully

  # Upload a Members list Test goes here.
  # Change the Members list type Test goes here.

