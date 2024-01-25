Feature: Direct Partnership Happy Paths
  As a user of the PAR service, I  want to be able to view/manage partnerships, So I can comply with the BEIS standards for goods and services

  @regression @dashboard @cookies
  Scenario: Verify a User can Log in and Accept Analytical Cookies Successfully (Happy Path - PAR-2331)
    Given the user is on the PAR home page
    And the user visits the login page
    When the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    # Accecpt the Analytic Cookies
    When the user accepts the analytics cookies
    Then analytical cookies have been accepted successfully

  @regression @direct @deletePartnership
  Scenario: Verify Partnership Information can be changed During Application Process, Nominated and then Deleted Successfully (Happy Path - PAR-2277)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    When the user creates a new "Direct" partnership application with the following details:
      | Authority | Partnership Info | AddressLine1  | AddressLine2 | Town | County     | Country        | Nation  | Postcode | Title | Firstname | Lastname | WorkNumber   | MobileNumber | Email                    |
      | Lower     | For Deletion     | 04 New Street | New Build    | Bury | Lancashire | United Kingdom | England | BL4 0BG  | Dr    | Steph     | Smith    | 017043356901 |  07704502913 | par_business@example.com |
    Then the first part of the partnership application is successfully completed
    #second part of partnership application
    When the user searches for the last created partnership
    And the user completes the partnership application with the following details:
      | Business Description | ContactNotes | SIC Code            | No of Employees | Trading Name | Legal Entity Name | Legal entity Type | Company number |
      | Test Business        | Test Note.   | allow people to eat | 10 to 49        | Testing Co.  | Testing Co.       | Partnership       |       12345678 |
    Then the second part of the partnership application is successfully completed
    # Nominate the Partnership
    Given the user is on the PAR login page
    And the user logs in with the "secretary_state@example.com" user credentials
    When the user searches for the last created partnership
    And the user approves the partnership
    And the user searches again for the last created partnership
    Then the partnership is displayed with Status "Active" and Actions "Revoke partnership"
    # Delete the Partnership
    When the user Deletes the Partnership with the following reason: "Partnership is incorrect."
    Then the Partnership was Deleted Successfully

  @regression @partnershipapplication @direct @update @organisation @enforcement @inspectionplan @inspectionfeedback @deviationrequest @enquiry @advicenotice @legalEntities @testUpdates
  Scenario: Verify Direct Partnership application by authority and completion by new business (Happy Path - PAR-1826, PAR-1835, PAR-1836, PAR-1837, PAR-1845)
    Given the user is on the PAR home page
    And the user visits the login page
    When the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    # Apply for a new Partnership
    When the user creates a new "Direct" partnership application with the following details:
      | Authority | Partnership Info | AddressLine1  | AddressLine2 | Town    | County     | Country        | Nation  | Postcode | Title | Firstname | Lastname | WorkNumber   | MobileNumber | Email                    |
      | Upper     | Direct           | 32 Bramtom Rd | New Build    | Windsor | Lancashire | United Kingdom | England | SL4 5PN  | Dr    | David     | Smith    | 020569987021 | 074567899221 | par_business@example.com |
    Then the first part of the partnership application is successfully completed
    #second part of partnership application
    When the user searches for the last created partnership
    And the user completes the partnership application with the following details:
      | Business Description | ContactNotes | SIC Code            | No of Employees | Trading Name | Legal Entity Name | Legal entity Type | Company number |
      | Test Business        | Test Note.   | allow people to eat | 10 to 49        | Testing LTD  | Testing LTD       | Partnership       |       12345678 |
    Then the second part of the partnership application is successfully completed
    # Verify all the Partnership Details are Displayed
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created partnership
    Then the partnership application is completed successfully

  @regression @login @enforcement @inspectionplan @inspectionfeedback @deviationrequest @enquiry @advicenotice @direct @update @legalEntities
  Scenario: Verify Approval, Revokation and Restoration of Partnership journey (Happy Path - PAR-1846, PAR-1847, PAR-1848)
    Given the user is on the PAR login page
    And the user logs in with the "secretary_state@example.com" user credentials
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

  @regression @direct @update
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
      | Address1    | Address2 | Town | County             | Country        | Nation Value | Post Code | About the Organisation | SIC Code          | Trading Name |
      | 01 new road | Market   | Bury | Greater Manchester | United Kingdom | Scotland     | BL2 4BD   | Updated Info           | you sell cookies. | Name Update  |
    Then all of the Partnership details have been updated successfully

  @regression @direct @update @legalEntities
  Scenario: Verify the Nomination of Legal Entity Amendments for an Active Partnership (Happy Path - PAR-2311)
    Given the user is on the PAR login page
    And the user logs in with the "par_authority_manager@example.com" user credentials
    When the user searches for the last created partnership
    And the user Amends the legal entities with the following details:
      | Entity Type | Entity Name    |
      | Partnership | Amendment Test |
    Then the user verifies the amendments are created successfully with status "Confirmed by the Authority"
    # Confirm Amendments as the Business User
    Given the user is on the PAR login page
    And the user logs in with the "par_business@example.com" user credentials
    When the user searches for the last created partnership
    And the user confirms the legal entity amendments
    Then the user verifies the amendments are confirmed successfully with status "Confirmed by the Organisation"
    # Nominate the Amendments as the Help Desk User
    Given the user is on the PAR login page
    And the user logs in with the "secretary_state@example.com" user credentials
    When the user searches for the last created partnership Authority
    And the user nominates the legal entity amendments
    Then the user verifies the amendments are nominated successfully with status "Active"

  @regression @direct @update @legalEntities
  Scenario: Verify the Revocation and reinstatement of a Legal Entity for an Active Partnership (Happy Path - PAR-2312, PAR-2313)
    Given the user is on the PAR login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    When the user searches for the last created partnership Authority
    And the user revokes the legal entity with the reason "Test Revoke"
    Then the user verifies the legal entity was revoked successfully with status "Revoked"
    # Reinstate the Legal Entity
    When the user reinstates the legal entity
    Then the user verifies the legal entity was reinstated successfully with status "Active"

  @regression @direct @update @legalEntities
  Scenario: Verify the Removal of a Legal Entity from an Active Partnership (Happy Path - PAR-2314)
    Given the user is on the PAR login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    When the user searches for the last created partnership Authority
    And the user removes the legal entity
    Then the user verifies the legal entity was removed successfully

  @regression @direct @update
  Scenario: Add and Remove a New Primary Authority Contact for a Partnership Successfully (Happy Path - PAR-2242)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership Authority
    And the user adds a Primary Authority contact to be Invited with the following details:
      | Title | WorkNumber  | MobileNumber | ContactNotes       |
      | Mr    | 01706553019 |  07651044910 | Test contact note. |
    Then the new Primary Authority contact is added Successfully
    # Remove the new contact
    When the user removes the new Primary Authority contact
    Then the new Primary Authority contact is removed Successfully

  @regression @direct @update
  Scenario: Add and Remove an Organisation Contact for a Partnership Successfully (Happy Path - PAR-2244)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership Authority
    And the user adds a new Organisation contact to be Invited with the following details:
      | Title | WorkNumber  | MobileNumber | ContactNotes       |
      | Mrs   | 01755892240 |  07651044912 | Test contact note. |
    Then the new Organisation contact is added Successfully
    # Remove the new contact
    When the user removes the new Organisation contact
    Then the new Organisation contact is removed Successfully

  @regression @inspectionplan @enforcement @deviationrequest @inspectionfeedback
  Scenario: Verify Upload of Inspection Plan (Happy Path - PAR-1856)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user uploads an inspection plan against the partnership with the following details:
      | Title              | Description |
      | Inspection Title 1 | Test 1      |
    Then the inspection plan is uploaded successfully

  @regression @inspectionplan
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
      | Title              | Type of Advice         | Reg Function      | Description         |
      | Partnership Advice | Background information | Alphabet learning | Advice description. |
    Then the advice notice it uploaded successfully and set to active
    # Edit Advice Notice
    When the user selects the edit advice action link
    And the user edits the advice notice with the following details:
      | Title                     | Type of Advice                                | Description                |
      | Partnership Advice Update | Primary Authority advice for the organisation | Advice description update. |
    Then the advice notice it updated successfully
    # Archive Advice Notice
    When the user archives the advice notice with the following reason "Advice Complete."
    Then the advice notice is archived successfully

  @regression @advicenotice
  Scenario: Verify Upload and Removal of an Advice Notice (Happy Path - PAR-1876)
    Given the user is on the PAR login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    When the user searches for the last created partnership
    And the user uploads an advice notice against the partnership with the following details:
      | Title              | Type of Advice         | Reg Function      | Description         |
      | Notice for Removal | Background information | Alphabet learning | Advice description. |
    Then the advice notice it uploaded successfully and set to active
    # Remove Advice Notice
    When the user removes the advice notice with the following reason "Advice no long needed."
    Then the advice notice is removed successfully

  @regression @advicenotice
  Scenario: Verify Upload of Advice Notice (Happy Path - PAR-1873)
    Given the user is on the PAR login page
    And the user logs in with the "secretary_state@example.com" user credentials
    When the user searches for the last created partnership
    And the user uploads an advice notice against the partnership with the following details:
      | Title          | Type of Advice         | Reg Function      | Description  |
      | Advice Title 1 | Background information | Alphabet learning | Test Advice. |
    Then the advice notice it uploaded successfully and set to active

  @regression @enforcement
  Scenario: Verify the Sending and Approval of a Notification of Proposed Enforcement (Happy Path - PAR-1852, PAR-1853, PAR-1854)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user creates an enforcement notice against the partnership with the following details:
      | Enforcement Action | Title               | Regulatory Function | Description                  | Attachment |
      | Proposed           | Enforcement Title 1 | Alphabet learning   | Test Enforcement Description | link.txt   |
    Then all the fields for the enforcement notice are updated correctly
    #	Approve the Enforcement Notice
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user selects the last created enforcement notice
    And the user approves the enforcement notice
    Then the enforcement notice is set to approved status

  @regression @enforcement
  Scenario: Verify a Help Desk user can remove an Enforcement Notice Successfully (Happy Path - PAR-1855)
    Given the user is on the PAR login page
    And the user logs in with the "secretary_state@example.com" user credentials
    When the user searches for the last created enforcement notice
    And the user removes the enforcement notice
    Then the enforcement notice is removed successfully

  @regression @enforcement
  Scenario: Verify an Enforcement Notice can be Blocked (Happy Path - PAR-1970)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user creates an enforcement notice against the partnership with the following details:
      | Enforcement Action | Title                            | Regulatory Function | Description       | Attachment |
      | Proposed           | Enforcement Notice to be Blocked | Alphabet learning   | Test Enforcement. | link.txt   |
    Then all the fields for the enforcement notice are updated correctly
    # Block the Enforcement Notice
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
      | Description                         |
      | Test Enforcement Notice Discussion. |
    Then the Enquiry is created Successfully
    # Login as the Authority and Verify the General Enquiry
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created general enquiry
    Then the general enquiry is recieved successfully

  @regression @deviationrequest
  Scenario: Verify a Deviation Request can be Blocked (Happy Path - PAR-2275)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user submits a deviation request against an inspection plan with the following details:
      | Description                           |
      | Test Deviation Request to be Blocked. |
    Then the Deviation Request is created Successfully
    # Login as the Authority and Verify the Deviation Request
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
      | Description             |
      | Test Deviation Request. |
    Then the Deviation Request is created Successfully
    # Login as primary authority and check and approve deviation request
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created deviation request
    Then the user successfully approves the deviation request
    # Submit a Response as the Authority
    When the user submits a response to the deviation request with the following details:
      | Description         |
      | Authority Response. |
    Then the response is displayed successfully
    # Submit a Response as the Enforcement Officer
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created deviation request
    And the user sends a reply to the deviation request message with the following details:
      | Description                |
      | Enforcement Officer Reply. |
    Then the response is displayed successfully
    # Submit a Response as the Help Desk
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created deviation request
    And the user sends a reply to the deviation request message with the following details:
      | Description         |
      | Help Desk Response. |
    Then the response is displayed successfully
    # Login as authority and check message received correctly
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created deviation request
    Then the deviation reply received successfully

  @regression @inspectionfeedback
  Scenario: Verify User can Submit feedback following an Inspection plan (Happy Path - PAR-1858, PAR-1860)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user submits an inspection feedback against the inspection plan with the following details:
      | Description               |
      | Test Inspection Feedback. |
    Then the inspection feedback is created successfully
    #	Login as primary authority and check and approve inspection feedback
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created inspection feedback
    Then the user successfully approves the inspection feedback
    # Submit a Response to the Inspection Feedback
    When the user submits a response to the inspection feedback with the following details:
      | Description              |
      | Test Authority Response. |
    Then the inspection feedback response is displayed successfully
    # Submit a Reply to the Insepction Feedback
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created inspection feedback
    And the user sends a reply to the inspection feedback message with the following details:
      | Description             |
      | Test Enforcement Reply. |
    Then the inspection feedback response is displayed successfully
    # Submit a Reply to the Insepction Feedback
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created inspection feedback
    And the user sends a reply to the inspection feedback message with the following details:
      | Description              |
      | Test Help Desk Response. |
    Then the inspection feedback response is displayed successfully
    # Login as the Authority and check Message Recieved Successfully.
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created inspection feedback
    Then the inspection feedback response is displayed successfully

  @regression @enquiry
  Scenario: Verify User can Submit a general enquiry (Happy Path - PAR-1861)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user submits a general enquiry with the following details:
      | Description           |
      | Test General Enquiry. |
    Then the Enquiry is created Successfully
    # Submit a Response to the General Enquiry
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created general enquiry
    And the user submits a response to the general enquiry with the following details:
      | Description              |
      | Test Authority Response. |
    Then the general enquiry response is displayed successfully
    # Submit a Reply to the General Enquiry
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created general enquiry
    And the user sends a reply to the general enquiry with the following details:
      | Description             |
      | Test Enforcement Reply. |
    Then the general enquiry response is displayed successfully
    # Submit a Reply to the General Enquiry
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created general enquiry
    And the user sends a reply to the general enquiry with the following details:
      | Description           |
      | Test Help Desk Reply. |
    Then the general enquiry response is displayed successfully
    # Login as authority and check message received correctly
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created general enquiry
    Then the general enquiry response is displayed successfully

  @regression @inspectionplan
  Scenario: Verify Revocation and then Removal of an Inspection Plan (Happy Path - PAR-1866, PAR-1867)
    Given the user is on the PAR login page
    And the user logs in with the "secretary_state@example.com" user credentials
    When the user searches for the last created partnership
    And the user revokes the last created inspection plan
    Then the inspection plan is revoked successfully
    #remove the inspection plan
    When the user has revoked the last created inspection plan
    Then the inspection plan is successfully removed

