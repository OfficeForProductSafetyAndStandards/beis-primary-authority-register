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
Feature: Direct Partnership Sad Paths

  @regression @sadpath @partnershipapplication @sadupdate @sadLegalEntities @sadadvice @sadinspectionplan @sadenforcement @saddeviation @sadinspectionfeedback @sadenquiry
  Scenario: Verify a user receives Error Messages for required fields during the Partnership Application and Completion Process (Sad Path - PAR-2392, PAR-2393)
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
    When the user selects a "Direct" partnership type
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
      | ErrorMessage                                     |
      | You must enter the first line of your address    |
      | You must enter the town or city for this address |
      | You must enter a valid postcode                  |
    When the user enters an address with the following details:
      | AddressLine1 | AddressLine2 | Town       | County              | Country        | Nation  | Postcode |
      | 01 Bridge    | Town Hall    | Manchester | Greater Manachester | United Kingdom | England | BL2 6GH  |
    And the user leaves the contact details fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                           |
      | You must enter the first name for this contact.        |
      | You must enter the last name for this contact.         |
      | You must enter the work phone number for this contact. |
      | You must enter the email address for this contact.     |
    When the user enters a contact with the following details:
      | Title | Firstname | Lastname | WorkNumber  | MobileNumber | Email                   |
      | Dr    | David     | Smythe   | 02045678912 |  07745665913 | davidsmythe@example.com |
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
      | ErrorMessage                                     |
      | You must enter the first line of your address    |
      | You must enter the town or city for this address |
      | You must enter a valid postcode                  |
    When the user confirms the address details with the following:
      | AddressLine1 | AddressLine2 | Town       | County              | Country        | Nation  | Postcode |
      | 01 Bridge    | Town Hall    | Manchester | Greater Manachester | United Kingdom | England | BL2 6GH  |
    And the user leaves all contact details fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                           |
      | You must enter the first name for this contact.        |
      | You must enter the last name for this contact.         |
      | You must enter the work phone number for this contact. |
      | You must enter the email address for this contact.     |
    When the user confirms the primary contact details with the following:
      | Title | Firstname | Lastname | WorkNumber  | MobileNumber | Email                   | ContactNote           |
      | Dr    | David     | Smythe   | 02045678912 |  07745665913 | davidsmythe@example.com | Testing Contact Note. |
    And the user confirms the sic code
    When the user does not confirm the number of employees
    Then the user is shown the "You must select how many employees this business has." error message
    When the user confirms the number of employees
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

  @regression @sadpath @partnershipapplication @sadupdate @sadLegalEntities @sadadvice @sadinspectionplan @sadenforcement @saddeviation @sadinspectionfeedback @sadenquiry
  Scenario: Verify a user receives Error Messages for required fields when Nominating a Partnership (Sad Path - PAR-2394)
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

  @regression @sadpath @sadupdate
  Scenario: Verify a user receives Error Messages for required fields when Revoking and Reinstating a Partnership (Sad Path - PAR-2395)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "secretary_state@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the revoke partnership action link
    And the user leaves the revoke reason field empty
    Then the user is shown the "You must give a reason for revoking this partnership." error message
    When the user enters a revoke reason
    And the user searches again for the last created partnership
    Then the partnership is revoked successfully
    When the user selects the restore partnership action link
    And the user restores the revoked partnership
    And the user searches again for the last created partnership
    Then the partnership is restored successfully
    And the user signs out

  @regression @sadpath @sadupdate
  Scenario: Verify a user receives Error Messages for required fields when Updating a Partnerships Information (Sad Path - PAR-2400)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership Authority
    And the user does not update the information about the partnership field empty
    Then the user is shown the "You must enter some information about this partnership." error message
    When the user enters the following information about the partnership "Test Partnership Information."
    Then the information about the partnership is updated successfully
    When the user updates the partnership to bespoke but does not choose the regulatory functions
    Then the user is shown the "You must choose at least one regulatory function." error message
    When the user updates the regulatory function
    Then the regulatory function is updated successfully
    # Change Partnership Information page tp continue updating.
    When the user searches for the last created partnership Organisation
    And the user leaves the organisation address fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                     |
      | You must enter the first line of your address    |
      | You must enter the town or city for this address |
      | You must enter a valid postcode.                 |
    When the user updates the address with the following details:
      | AddressLine1 | AddressLine2 | Town       | County              | Country        | Nation  | Postcode |
      | 01 Bridge    | Town Hall    | Manchester | Greater Manachester | United Kingdom | England | BL2 6GH  |
    Then the organisation address is updated successfully
    When the user does not update the about the organisation field empty
    Then the user is shown the "You must enter a description for the business." error message
    When the user updates the organisation information with the following: "Test Organisation Information."
    Then the information about the organisation is updated successfully
    When the user clicks the add another trading name link but leaves the text field empty
    Then the user is shown the "You must enter the trading name for this organisation" error message
    When the user enters a new trading name: "New Trading Name"
    Then the new trading name is added successfully
    When the user edits the trading name but leaves the text field empty
    Then the user is shown the "You must enter the trading name for this organisation." error message
    When the user updates the trading name: "Updated Trading Name"
    Then the trading name is updated successfully
    And the user signs out

  @regression @sadpath @sadupdate @sadLegalEntities
  Scenario: Verify a user receives Error Messages for required fields when Amending Legal Entities for a Partnership (Sad Path - PAR-2401)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user does not choose the type of legal entity
    Then the user is shown the "Please choose whether this is a registered or unregistered legal entity." error message
    When the user chooses "A registered organisation" legal entity type but does not enter the number
    Then the user is shown the "Please enter the legal entity number." error message
    When the user chooses "A charity" legal entity type but does not enter the number
    Then the user is shown the "Please enter the legal entity number." error message
    When the user chooses the "An unregistered entity" legal entity type but does not choose the structure or enter the name
    Then the user is shown the following error messages:
      | ErrorMessage                                            |
      | You must choose which legal entity type you are adding. |
      | Please enter the name of the legal entity.              |
    When the user chooses the unregistered entity structure but does not enter the name
    Then the user is shown the "Please enter the name of the legal entity." error message
    When the user adds a legal entity amendment with the name: "Test Entity Amendment"
    And the user does not confirm the amendment
    Then the user is shown the "Please confirm the amendment to this partnership." error message
    When the user confirms the legal entity amendment
    Then the user verifies the amendments are created successfully with status "Confirmed by the Authority"
    And the user signs out
    # Confirm the Legal Entity Amendments as the Business User.
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership Authority
    And the user selects the confirm amendments link
    And the user does not confirm the amendment
    Then the user is shown the "Please confirm the amendment to this partnership." error message
    When the user confirms the legal entity amendment
    Then the user verifies the amendments are created successfully with status "Confirmed by the Organisation"
    And the user signs out
    # Nominate the Legal Entity Amendments as the Secretary of State User.
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "secretary_state@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership Authority
    And the user clicks the nominate amendments link
    And the user does not confirm the amendment
    Then the user is shown the "Please confirm the amendments to this partnership." error message
    When the user nominates the legal entity amendment
    Then the user verifies the amendments are created successfully with status "Active"
    And the user signs out

  @regression @sadpath @sadupdate
  Scenario: Verify a user receives Error Messages for required fields when Adding and Removing a Primary Authority Contact for a Partnership (Sad Path - PAR-2402)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership Authority
    And the user selects the add another authority contact link
    And the user leaves the contact details fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                           |
      | You must enter the first name for this contact.        |
      | You must enter the last name for this contact.         |
      | You must enter the work phone number for this contact. |
      | You must enter the email address for this contact.     |
    When the user enters the following authority contact details:
      | Title | Firstname | Lastname | WorkNumber  | MobileNumber | Email                    | ContactNotes |
      | Dr    | Sandra    | Smythe   | 02056698103 |  07798573404 | sandrasmythe@example.com | Test Note.   |
    Then the new contact is added successfully
    # Remove the new contact.
    When the user removes the new Primary Authority Contact
    Then the new Primary Authority contact is removed Successfully
    And the user signs out

  @regression @sadpath @sadupdate
  Scenario: Verify a user receives Error Messages for required fields when Adding and Removing an Organisation Contact for a Partnership (Sad Path - PAR-2403)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership Authority
    And the user selects the add another organisation contact link
    And the user leaves the contact details fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                           |
      | You must enter the first name for this contact.        |
      | You must enter the last name for this contact.         |
      | You must enter the work phone number for this contact. |
      | You must enter the email address for this contact.     |
    When the user enters the following authority contact details:
      | Title | Firstname | Lastname | WorkNumber  | MobileNumber | Email                  | ContactNotes |
      | Mrs   | Sarah     | Hardy    | 02056698234 |  07798573542 | sarahhardy@example.com | Test Note.   |
    Then the new contact is added successfully
    # Remove the new contact.
    When the user removes the new Organisation contact
    Then the new Organisation contact is removed Successfully
    And the user signs out

  @regression @sadpath @sadadvice
  Scenario: Verify a user receives Error Messages for required fields when Uploading Advice to a Partnership (Sad Path - PAR-2404)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the see all advice link
    And the user selects upload without choosing a file
    Then the user is shown the "Upload file(s) field is required." error message
    When the user uploads an advice file
    And the user does not enter advice details
    Then the user is shown the following error messages:
      | ErrorMessage                                                       |
      | You must provide a title for this advice document.                 |
      | You must choose what type of advice this is.                       |
      | You must provide a summary for this advice document.               |
      | You must choose which regulatory functions this advice applies to. |
    When the user enters the following advice details:
      | Title                | Type of Advice         | Reg Function      | Description       |
      | Sad Path Advice Test | Background information | Alphabet learning | Sad Path Testing. |
    Then the advice is created successfully
    And the user signs out

  @regression @sadpath @sadadvice
  Scenario: Verify a user receives Error Messages for required fields when Editing Advice to a Partnership (Sad Path - PAR-2405)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the see all advice link
    And the user selects the edit link
    And the user does not enter advice details
    Then the user is shown the following error messages:
      | ErrorMessage                                                       |
      | You must provide a title for this advice document.                 |
      | You must provide a summary for this advice document.               |
      | You must choose which regulatory functions this advice applies to. |
    When the user enters the following advice details:
      | Title                     | Type of Advice         | Reg Function      | Description            |
      | Sad Path Advice Edit Test | Background information | Alphabet learning | Sad Path Edit Testing. |
    Then the advice is updated successfully
    And the user signs out

  @regression @sadpath @sadadvice
  Scenario: Verify a user receives Error Messages for required fields when Archiving Advice for a Partnership (Sad Path - PAR-2406)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the see all advice link
    And the user selects the archive link
    And the user does not enter a reason for archiving
    Then the user is shown the "Please supply the reason for archiving this document." error message
    When the user enters a reason for archiving the advice
    Then the advice is archived successfully
    And the user signs out

  @regression @sadpath @sadadvice
  Scenario: Verify a user receives Error Messages for required fields when Removing Advice for a Partnership (Sad Path - PAR-2407)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the see all advice link
    And the user selects the remove link
    And the user does not enter a reason for removing
    Then the user is shown the "Please enter the reason you are removing this advice." error message
    When the user enters a reason for removing the advice
    Then the advice is removed successfully
    And the user signs out

  @regression @sadpath @sadinspectionplan @saddeviation @sadinspectionfeedback
  Scenario: Verify a user receives Error Messages for required fields when Uploading an Inspection Plan for a Partnership (Sad Path - PAR-2408)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the see all inspection plans link
    And the user selects the upload inspection plan link
    And the user attempts to upload an inspection plan without choosing a file
    Then the user is shown the "Upload file(s) field is required" error message
    When the user uploads an inspection plan file
    And the user does not enter inspection plan details
    Then the user is shown the following error messages:
      | ErrorMessage                                                |
      | You must provide a title for this inspection plan document. |
      | You must fill in the missing information.                   |
    When the user enters the following inspection plan details:
      | Title               | Description           |
      | Sad Path Inspection | Inspecting sad paths. |
    And the user does not enter an expiry date
    Then the user is shown the "You must enter the date the inspection plan expires e.g. 30 - 01 - 2022" error message
    When the user enters an inspection plan expiry date
    Then the inspection plan is created successfully
    And the user signs out

  @regression @sadpath @sadinspectionplan
  Scenario: Verify a user receives Error Messages for required fields when Editing an Inspection Plan for a Partnership (Sad Path - PAR-2409)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the see all inspection plans link
    And the user clicks the edit link
    And the user does not enter inspection plan details
    Then the user is shown the following error messages:
      | ErrorMessage                                                |
      | You must provide a title for this inspection plan document. |
      | You must fill in the missing information.                   |
    When the user enters the following inspection plan details:
      | Title                      | Description                  |
      | Sad Path Inspection Update | Inspecting sad paths Update. |
    And the user does not enter an expiry date
    Then the user is shown the "You must enter the date the inspection plan expires e.g. 30 - 01 - 2022" error message
    When the user enters an inspection plan expiry date
    Then the inspection plan is created successfully
    And the user signs out

  @regression @sadpath @sadenforcement
  Scenario: Verify a user receives Error Messages for required fields when Raising an Enforcement Notice (Sad Path - PAR-2426)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user clicks the Send a notification of a proposed enforcement action link
    And the user leaves the enforcement officer contact detail fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                           |
      | You must enter the first name for this contact.        |
      | You must enter the last name for this contact.         |
      | You must enter the work phone number for this contact. |
    When the user enters the following enforcement officer contact details:
      | Firstname | Lastname | Workphone   |
      | Grover    | Muppet   | 01723456789 |
    And the user does not enter the name of the legal entity
    Then the user is shown the "You must choose a legal entity." error message
    When the user enters the name of the legal entity
    And the user does not provide a summary of the enforcement details
    Then the user is shown the "You must enter a summary description for this notice of enforcement action." error message
    When the user enters a summary with the enforcement details:
      | Enforcement Action | Summary       |
      | Proposed           | Test summary. |
    And the user leaves the enforcement action detail fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                                                 |
      | Please choose which regulatory functions this enforcement action relates to. |
      | You must enter a title for this enforcement action.                          |
      | You must enter the details of this enforcement action.                       |
    When the user enters the following details for an enforcement action:
      | Title                            | Regulatory Function | Description       | Attachment |
      | Enforcement Notice to be Blocked | Alphabet learning   | Test Enforcement. | link.txt   |
    Then the enforcement notice is created successfully
    And the user signs out

  @regression @sadpath @sadenforcement
  Scenario: Verify a user receives Error Messages for required fields when Blocking an Enforcement Notice (Sad Path - PAR-2427)
    Given the user is on the PAR home page
    When the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    When the user selects the last created enforcement notice
    And the user selects the block button without entering a reason to block
    Then the user is shown the "You must explain your reason for blocking this notice." error message
    When the user blocks the enforcement notice with the following reason: "Test Block."
    Then the enforcement notice is set to blocked status
    And the user signs out

  @regression @sadpath @sadenforcement
  Scenario: Verify a user receives Error Messages for required fields when Removing an Enforcement Notice (Sad Path - PAR-2428)
    Given the user is on the PAR home page
    When the user is on the PAR login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created enforcement notice
    And the user selects the Remove enforcement action link
    And the user does not select a reason or enter a description for the removal
    Then the user is shown the following error messages:
      | ErrorMessage                                                      |
      | You must select a reason for removal.                             |
      | Please give a description of why this removal is being requested. |
    When the user provides the following reason and a description for the removal:
      | RemovalReason                   | RemovalDescription |
      | This is a duplicate enforcement | Test Removal.      |
    And the user does not confirm they want to remove the enforcement notice
    Then the user is shown the "You must confirm you wish to remove this item." error message
    When the user confirms they want the enforcement notice removing
    Then the enforcement notice is removed successfully
    And the user signs out

  @regression @sadpath @saddeviation
  Scenario: Verify a user receives Error Messages for required fields when requesting a Deviation from the Inspection Plan (Sad Path - PAR-2429)
    Given the user is on the PAR home page
    When the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user clicks the Request to deviate from the inspection plan link
    And the user leaves the enforcement officer contact detail fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                           |
      | You must enter the first name for this contact.        |
      | You must enter the last name for this contact.         |
      | You must enter the work phone number for this contact. |
    When the user enters the following enforcement officer contact details:
      | Firstname | Lastname | Workphone   |
      | Grover    | Muppet   | 01723456789 |
    And the user does not enter the deviation request details
    Then the user is shown the following error messages:
      | ErrorMessage                                                 |
      | You must enter the details of this enquiry.                  |
      | You must submit a proposed inspection plan for this enquiry. |
    When the user enters the deviation request with the following details:
      | Description         |
      | Sad Deviation Test. |
    Then the deviation request is created successfully
    And the user signs out

  @regression @sadpath @saddeviation
  Scenario: Verify a user receives Error Messages for required fields when blocking a Deviation from the Inspection Plan Request (Sad Path - PAR-2430)
    Given the user is on the PAR home page
    When the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created deviation request
    And the user selects blocks the deviation without providing a reason
    Then the user is shown the "You must explain your reason for blocking this deviation request." error message
    When the user blocks the deviation request with the following reason: "Test Block"
    Then the deviation request is set to blocked status
    And the user signs out

  @regression @sadpath @saddeviation
  Scenario: Verify a user receives Error Messages for required fields when responding to a Deviation from the Inspection Plan Request (Sad Path - PAR-2431)
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created partnership
    And the user submits a deviation request against an inspection plan with the following details:
      | Description                 |
      | Sad Deviation Request Test. |
    Then the Deviation Request is created Successfully
    And the user signs out
    # Approve the Deviation Request
    Given the user is on the PAR home page
    When the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created deviation request
    Then the user successfully approves the deviation request
    # Submit a Response as the Authority User.
    When the user selects the approved deviation request
    And the user tries to submit a response without any details
    Then the user is shown the "You must enter a response." error message
    When the user enters the following response: "Authority Response."
    Then the response is displayed successfully
    And the user signs out
    # Submit a Response as the Enforcement Officer.
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created deviation request
    And the user tries to submit a response without any details
    Then the user is shown the "You must enter a response." error message
    When the user enters the following response: "Enforcement Officer Response."
    Then the response is displayed successfully
    And the user signs out
    # Submit a REsponse as the Help Desk User.
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created deviation request
    And the user tries to submit a response without any details
    Then the user is shown the "You must enter a response." error message
    When the user enters the following response: "Enforcement Officer Response."
    Then the response is displayed successfully
    And the user signs out

  @regression @sadpath @sadinspectionfeedback
  Scenario: Verify a user receives Error Messages for required fields when submitting Feedback following an Inspection (Sad Path - PAR-2432)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user clicks the Submit Feedback following an inspection link
    And the user leaves the enforcement officer contact detail fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                           |
      | You must enter the first name for this contact.        |
      | You must enter the last name for this contact.         |
      | You must enter the work phone number for this contact. |
    When the user enters the following enforcement officer contact details:
      | Firstname | Lastname | Workphone   |
      | Grover    | Muppet   | 01723456789 |
    And the user does not enter inspection plan feedback
    Then the user is shown the "You must enter the details of this enquiry." error message
    When the user enters the following inspection plan feedback "Sad Inspection Plan Feedback Test."
    Then the inspection plan feedback is created successfully
    And the user signs out

  @regression @sadpath @sadinspectionfeedback
  Scenario: Verify a user receives Error Messages for required fields when responding to Feedback following an Inspection (Sad Path - PAR-2433)
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created inspection feedback
    And the user tries to submit a response without any details
    Then the user is shown the "You must enter a response." error message
    When the user enters the following response: "Authority Response."
    Then the inspection feedback response is displayed successfully
    And the user signs out
    # Submit a Response as the Enforcement Officer.
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created inspection feedback
    And the user tries to submit a response without any details
    Then the user is shown the "You must enter a response." error message
    When the user enters the following response: "Enforcement Officer Response."
    Then the inspection feedback response is displayed successfully
    And the user signs out
    # Submit a Response as the Help Desk User.
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created inspection feedback
    And the user tries to submit a response without any details
    Then the user is shown the "You must enter a response." error message
    When the user enters the following response: "Help Desk Response."
    Then the inspection feedback response is displayed successfully
    And the user signs out

  @regression @sadpath @sadenquiry
  Scenario: Verify a user receives Error Messages for required fields when sending a General Enquiry (Sad Path - PAR-2434)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user clicks the Send a general enquiry to the primary authority link
    And the user leaves the enforcement officer contact detail fields empty
    Then the user is shown the following error messages:
      | ErrorMessage                                           |
      | You must enter the first name for this contact.        |
      | You must enter the last name for this contact.         |
      | You must enter the work phone number for this contact. |
    When the user enters the following enforcement officer contact details:
      | Firstname | Lastname | Workphone   |
      | Grover    | Muppet   | 01723456789 |
    And the user does not enter the general enquiry details
    Then the user is shown the "You must enter the details of this enquiry." error message
    When the user enters the following general enquiry details "Sad General Enquiry Test."
    Then the general enquiry is created successfully
    And the user signs out

  @regression @sadpath @sadenquiry
  Scenario: Verify a user receives Error Messages for required fields when responding to a General Enquiry (Sad Path - PAR-2435)
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user searches for the last created general enquiry
    And the user tries to submit a response without any details
    Then the user is shown the "You must enter a response." error message
    When the user enters the following response: "Authority Response."
    Then the general enquiry response is displayed successfully
    And the user signs out
    # Submit a Response as the Enforcement Officer.
    Given the user is on the PAR login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    When the user searches for the last created general enquiry
    And the user tries to submit a response without any details
    Then the user is shown the "You must enter a response." error message
    When the user enters the following response: "Enforcement Officer Response."
    Then the general enquiry response is displayed successfully
    And the user signs out
    # Submit a Response as the Help Desk User.
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created general enquiry
    And the user tries to submit a response without any details
    Then the user is shown the "You must enter a response." error message
    When the user enters the following response: "Help Desk Response."
    Then the general enquiry response is displayed successfully
    And the user signs out

  @regression @sadpath @sadinspectionplan
  Scenario: Verify a user receives Error Messages for required fields when Revoking an Inspection Plan for a Partnership (Sad Path - PAR-2410)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the see all inspection plans link
    And the user clicks the revoke link
    And the user does not enter a reason for revoking
    Then the user is shown the "Please supply the reason for revoking this document." error message
    When the user enters a reason to revoke the inspection plan
    Then the inspection plan is revoked successfully
    And the user signs out

  @regression @sadpath @sadinspectionplan
  Scenario: Verify a user receives Error Messages for required fields when Removing an Inspection Plan for a Partnership (Sad Path - PAR-2411)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the see all inspection plans link
    And the user clicks the remove link
    And the user does not enter a reason for removing
    Then the user is shown the "Please enter the reason you are removing this inspection plan." error message
    When the user enters a reason to remove the inspection plan
    Then the inspection plan is removed successfully
    And the user signs out

  @regression @sadpath @saddeviationrequest
  Scenario: Verify a user receives Error Messages when attempting to send a Deviation Request without an Inspection Plan (Sad Path - PAR-2424)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the Request to deviate from the inspection plan link
    Then the user is shown the "You can not complete this journey because this partnership doesn't have any inspection plans." error message
    And the user signs out

  @regression @sadpath @sadinspectionfedback
  Scenario: Verify a user receives Error Messages when attempting to send Inspection Feedback without an Inspection Plan (Sad Path - PAR-2425)
    Given the user is on the PAR home page
    When the user visits the login page
    And the user logs in with the "par_enforcement_officer@example.com" user credentials
    Then the user is on the dashboard page
    When the user searches for the last created partnership
    And the user selects the Submit feedback following an inspection link
    Then the user is shown the "You can not complete this journey because this partnership doesn't have any inspection plans." error message
    And the user signs out
