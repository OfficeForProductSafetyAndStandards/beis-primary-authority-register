Feature: Coordinated Partnership Happy Paths
  As a user of the PAR service, I  want to be able to view/manage partnerships, So I can comply with the BEIS standards for goods and services

  @regression @partnershipapplication @coordinated @authorityManagement
  Scenario: Verify Coordinated Partnership application by authority and completion by new business (Happy Path - PAR-1838, PAR-1839, PAR-1840, PAR-1841)
    Given the user is on the PAR home page
    And the user visits the login page
    And the user logs in with the "par_authority_manager@example.com" user credentials
    Then the user is on the dashboard page
    When the user creates a new "Co-ordinated" partnership application with the following details:
      | Authority | Partnership Info | AddressLine1 | AddressLine2 | Town | County     | Country        | Nation  | Postcode | Title | Firstname | Lastname | WorkNumber   | MobileNumber | Email                    |
      | Upper     | Coordinated      | 02 New Road  | New Build    | Bury | Lancashire | United Kingdom | England | SL4 5PN  | Mr    | Derrick   | Fletcher | 020569987021 | 074567899221 | par_business@example.com |
    Then the first part of the partnership application is successfully completed
    #second part of partnership application
    When the user searches for the last created partnership
    And the user completes the partnership application with the following details:
      | Business Description | ContactNotes | SIC Code            | Member List Size | Trading Name | Legal Entity Name | Legal entity Type | Company number |
      | Testing Business     | Test Note.   | allow people to eat | Medium           | Test HQ      | Test HQ           | Partnership       |       12345678 |
    Then the second part of the partnership application is successfully completed
    And the user signs out
    # Verify all the Partnership Details are Displayed
    Given the user is on the PAR login page
    And the user logs in with the "par_authority_manager@example.com" user credentials
    When the user searches for the last created partnership
    Then the partnership application is completed successfully
    And the user signs out

  @regression @coordinated @organisationMember @authorityManagement
  Scenario: Successfully Nominate a Coordinated Partnership (Happy Path - PAR-2261)
    Given the user is on the PAR login page
    And the user logs in with the "secretary_state@example.com" user credentials
    When the user searches for the last created partnership
    And the user approves the partnership
    And the user searches again for the last created partnership
    Then the partnership is displayed with Status "Active" and Actions "Revoke partnership"
    And the user signs out

  @regression @coordinated @organisationMember
  Scenario: Verify Addition of a Single Member Organisation to a Coordinated partnership (Happy Path - PAR-1868)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user adds a single member organisation to the patnership with the following details:
      | Organisation Name    | Address Line 1 | Address Line 2 | Town City  | County             | Country        | Nation | Postcode | Title | WorkNumber  | MobileNumber | Legal Entity Type | Legal Entity Name |
      | Testing Organisation | 02 New Street  | Market Hall    | Manchester | Greater Manchester | United Kingdom | Wales  | BL2 4BL  | Dr    | 02345678901 |  07890123456 | Sole trader       | Testing Co.       |
    Then the user member organistion has been added to the partnership successfully
    And the user signs out

  @regression @coordinated @organisationMember
  Scenario: Verify the Update of a Single Member Organisation for a Coordinated partnership (Happy Path - PAR-1969)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user updates a single member organisation of the patnership with the following details:
      | Organisation Name    | Address Line 1 | Address Line 2 | Town City  | County             | Country        | Nation | Postcode | Title | WorkNumber   | MobileNumber | Legal Entity Type | Legal Entity Name |
      | Testers Organisation | 03 New Street  | Market Hall    | Manchester | Greater Manchester | United Kingdom | Wales  | BL2 4BL  | Mr    | 020455669921 |  07009156780 | Sole trader       | Tester Co.        |
    Then the member organistion has been updated successfully
    And the user signs out

  @regression @coordinated @organisationMember
  Scenario: Verify the Cessation of a Single Member Organisation for a Coordinated partnership (Happy Path - PAR-1869)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user Ceases a single member organisation of the patnership with the current date
    Then the member organistion has been Ceased successfully
    And the user signs out
	
  @regression @coordinated @organisationMember
  Scenario: Verify the Upload of a Members List to a Coordinated partnership (Happy Path - PAR-1872)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user Uploads a members list to the coordinated partnership with the following file "memberslist.csv"
    Then the members list is uploaded successfully
    And the user signs out
  
  @regression @coordinated @organisationMember
  Scenario: Verify a Coordinated Partnerships Members List Type can be Changed Successfully (Happy Path - PAR-2325)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created partnership
    And the user changes the members list type to "externalRequest"
    Then the members list type is changed successfully
    And the user signs out
    