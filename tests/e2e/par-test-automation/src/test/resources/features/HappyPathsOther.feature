Feature: Other Happy Paths
  As a user of the PAR service, I  want to be able to view/manage partnerships, So I can comply with the BEIS standards for goods and services

  @regression @publicRegistrySearch
  Scenario: Verify a Non-registered User can Search the Public Register (Happy Path - PAR-2079)
    Given the user is on the PAR home page
    When the user is on the search for a partnership page
    And the user can search for a PA Organisation Trading name Company number
    Then the user is shown the information for that partnership

  @regression @publicRegistrySearch
  Scenario: Verify a Registered User can Search the Public Register (Happy Path - PAR-2079)
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    And the user clicks the PAR Home page link
    When the user is on the search for a partnership page
    And the user can search for a PA Organisation Trading name Company number
    Then the user is shown the information for that partnership

  @regression @authority @authorityManagement @usermanagement
  Scenario: Verify Addition/Update of Authority (Happy Path - PAR-1849, PAR-1850)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user creates a new authority with the following details:
      | Authority Type | ONS Code | Regulatory Function | AddressLine1  | AddressLine2 | Town    | County         | Country        | Nation  | Postcode |
      | Council Area   | 43453465 | Cookie control      | 32 Bramtom Rd | new build    | Windsor | Greater London | United Kingdom | England | SL4 5PN  |
    Then the authority is created sucessfully
    #Update All Fields for newly created Authority
    When the user searches for the last created authority
    And the user updates all the fields for newly created authority
    Then the update for the authority is successful

  @regression @authority @authorityManagement
  Scenario: Verify The Transfer of a Partnership from an Existing Authority to a New Authority (Happy Path - PAR-2287)
    Given the user is on the PAR login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    When the user searches for an Authority with the same Regulatory Functions "Upper West Side Borough Council"
    And the user completes the partnership transfer process
    Then the partnership is transferred to the new authority successfully

  @regression @organisation
  Scenario: Verify Update of Organisation (Happy Path - PAR-1851)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user searches for the last created organisation
    And the user updates all the fields for last created organisation
    Then all the fields are updated correctly

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
    And the user logs in with the "senior_administrator@example.com" user credentials
    When the user is on the Manage a subscription list page
    And the user enters a new email to add to the list "new_user@authority.co.uk"
    Then the user can verify the new email was added successfully

  @regression @helpDesk @PARNewsSubscription
  Scenario: Verify a Helpdesk user can remove an eisting Email from the PAR News Subscription List (Happy Path - PAR-2074)
    Given the user is on the PAR login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    When the user is on the Manage a subscription list page
    And the user enters an email to be removed from the list "new_user@authority.co.uk"
    Then the user can verify the email was removed successfully

  @regression @helpDesk @PARNewsSubscription
  Scenario: Verify a Helpdesk user can replace the PAR News Subscription List with a new List (Happy Path - PAR-2075)
    Given the user is on the PAR login page
    And the user logs in with the "senior_administrator@example.com" user credentials
    When the user is on the Manage a subscription list page
    And the user enters a list of new emails to replace the subscription list
    Then the user can verify an email from the original list was removed successfully

	@regression @usermanagement
	Scenario: Verify a Member User can be assigned the Manager Role and assign another Member the Manager Role Successfully (Happy Path - PAR-2378)
		Given the user is on the PAR home page
    When the user visits the login page
		And the user logs in with the "par_helpdesk@example.com" user credentials
		When the user searches for the "par_authority_manager@example.com" user account
		And the user clicks the manage contact link
		Then the user can view the user account successfully
		# Change the user role.
		When the user changes the users role to "Authority Manager"
		Then the user role was changed successfully
		# Verify the Authority Manager can change the Authority Member's role.
		Given the user is on the PAR login page
		And the user logs in with the "par_authority_manager@example.com" user credentials
		When the user searches for the "par_authority@example.com" user account
		And the user clicks the manage contact link
		Then the user can view the user account successfully
		# Change the user role.
		When the user changes the users role to "Authority Manager"
		Then the user role was changed successfully
	
	@regression @usermanagement
	Scenario: Verify a User can add and remove an Authority membership for another user Successfully (Happy Path - PAR-2379)
		Given the user is on the PAR home page
    When the user visits the login page
		And the user logs in with the "senior_administrator@example.com" user credentials
		When the user searches for the "par_authority@example.com" user account
		And the user clicks the manage contact link
		Then the user can view the user account successfully
		# Add a membership.
		When the user adds a new Authority membership
		Then the Authority membership was added successfully
		# Remove a membership.
		When the user removes the last added Authority membership
		Then the Authority membership was removed successfully
		
	@regression @usermanagement
	Scenario: Verify a Senior Administrator can Block and Reinstate a User Account Successfully (Happy Path - PAR-2382)
		Given the user is on the PAR home page
    When the user visits the login page
		And the user logs in with the "senior_administrator@example.com" user credentials
		When the user searches for the "national_regulator@example.com" user account
		And the user clicks the manage contact link
		Then the user can view the user account successfully
		# Block the User Account.
		When the user blocks the user account
		Then the user verifies the account was blocked successfully
		# Attempt to log in with the blocked user account.
		Given the user is on the PAR login page
		And the user logs in with the "national_regulator@example.com" user credentials
		Then the user cannot sign in and receives an error message
		# Sign in as the Senior Adminstrator again.
		Given the user is on the PAR home page
    When the user visits the login page
		And the user logs in with the "senior_administrator@example.com" user credentials
		When the user searches for the "national_regulator@example.com" user account
		And the user clicks the manage contact link
		Then the user can view the user account successfully
		# Re-activate the user account.
		When the user reinstates the user account
		Then the user verifies the account is reinstated successfully
		# Attempt to log in with the re-activated user account.
		Given the user is on the PAR login page
		And the user logs in with the "national_regulator@example.com" user credentials
		Then the user is on the dashboard page
		
  @regression @helpDesk @usermanagement
  Scenario: Verify the Addition and Update of a New Persons Contact Record as a Help Desk User (Happy Path - PAR-2097)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user creates a new person:
      | Title | WorkNumber | MobileNumber |
      | Mr    |      01204 |              |
    Then the user can verify the person was created successfully and can send an account invitation
    # Update the User's Contact Details.
    When the user searches for an existing person successfully
    And the user updates an existing person:
      | Title | WorkNumber  | MobileNumber |
      | Dr    | 01204996501 |  07405882265 |
    Then the user can verify the person was updated successfully and can send an account invitation
	
	@regression @usermanagement
  Scenario: Verify Completion of User Creation journey (Happy Path - PAR-1904)
    Given the user is on the PAR login page
    And the user logs in with the "par_admin@example.com" user credentials
    When the user visits the maillog page and extracts the invite link
    #And the user is on the PAR login page
    And the user follows the invitation link
    And the user completes the user creation journey
    Then the user journey creation is successful
	
	@regression @usermanagement @userAccount
  Scenario: Verify a User can Change their User Account Email Address (Happy Path - PAR-2323)
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    When the user updates their user account email address to "par_authority_2@example.com"
    Then the user can verify the new email address is displayed on the header
    # Log in with the new Email Address and revertback to the original Email Address
    Given the user is on the PAR login page
    And the user logs in with the "par_authority_2@example.com" user credentials
    When the user updates their user account email address to "par_authority@example.com"
    Then the user can verify the new email address is displayed on the header
    # Log in with the reverted Email Address
    Given the user is on the PAR login page
    And the user logs in with the "par_authority@example.com" user credentials
    Then the user can verify the new email address is displayed on the header
	
  @regression @helpDesk @statistics
  Scenario: Verify the Help Desk User can see the Statistics Page (Happy Path - PAR-2315)
    Given the user is on the PAR login page
    And the user logs in with the "par_helpdesk@example.com" user credentials
    When the user navigates to the statistics page
    Then the statistics page is dispalyed successfully

  @regression @homePageLinks
  Scenario: Verify a User can view Guidance for Local Regulation Primary Authority Successfully (Happy Path - PAR-2289)
    Given the user is on the PAR home page
    When the user selects the Read more about Primary Authority link
    Then the user is taken to the GOV.UK Guidance page for Local regulation Primary Authority Successfully

  @regression @homePageLinks
  Scenario: Verify a User can view the Collection of Primary Authority Documents Successfully (Happy Path - PAR-2290)
    Given the user is on the PAR home page
    When the user selects the Access tools and templates for local authorities link
    Then the user is taken to the GOV.UK Collection page for Primary Authority Documents Successfully

  @regression @homePageLinks
  Scenario: Verify a User can view the Terms and Conditions Successfully (Happy Path - PAR-2291)
    Given the user is on the PAR home page
    When the user selects the Terms and Conditions link
    Then the user is taken to the GOV.UK Guidance page for Primary Authority terms and conditions Successfully

  @regression @homePageLinks
  Scenario: Verify a User can view and Accept Analytics Cookies Successfully (Happy Path - PAR-2292)
    Given the user is on the PAR home page
    When the user selects the Cookies link
    Then the user is taken to the Cookies page and can accept the Analytics Cookies Successfully

  @regression @homePageLinks
  Scenario: Verify a User can view the OPSS Privacy Notice Successfully (Happy Path - PAR-2293)
    Given the user is on the PAR home page
    When the user selects the Privacy link
    Then the user is taken to the GOV.UK Corporate report OPSS Privacy notice page Successfully

  @regression @homePageLinks
  Scenario: Verify a User can view the Primary Authority Register Accessibility Statement Successfully (Happy Path - PAR-2294)
    Given the user is on the PAR home page
    When the user selects the Accessibility link
    Then the user is taken to the GOV.UK Guidance page for the Primary Authority Register accessibility statement Successfully

  @regression @homePageLinks
  Scenario: Verify a User can view the Open Government Licence Successfully (Happy Path - PAR-2295)
    Given the user is on the PAR home page
    When the user selects the Open Government Licence link
    Then the user is taken to the Open Government Licence for public sector information page Successfully

  @regression @homePageLinks
  Scenario: Verify a User can view the Crown Copyright Successfully (Happy Path - PAR-2296)
    Given the user is on the PAR home page
    When the user selects the Crown copyright link
    Then the user is taken to the Crown copyright page Successfully
