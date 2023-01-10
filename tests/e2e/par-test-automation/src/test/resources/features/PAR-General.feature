Feature:
  As a user of the PAR service
  I  want to be able to view/manage partnerships
  So I can comply with the BEIS standards for goods and services
  
 @regression @partnershipapplication @direct @update @usermanagement @organisation @enforcement
 Scenario: Verify Direct Partnership application by authority and completion by new business (Happy Path - PAR-1826, PAR-1835, PAR-1836, PAR-1837, PAR-1845)
  Given the user is on the PAR home page
  And the user visits the login page
  And the user logs in with the "par_authority@example.com" user credentials
  Then the user is on the dashboard page
  When the user creates a new "Direct" partnership application with the following details:
   | Authority							| Partnership Info			| firstname	 	| lastname	| phone		| addressline1	| town		| postcode	|
   | Lower                	| Test partnership info | Test First	| test last	| 9797878	| 32 Bramtom Rd	| Windsor	| SL4 5PN		|
  Then the first part of the partnership application is successfully completed
  
  #second part of partnership application 
  When the user searches for the last created partnership
  And the user completes the partnership application with the following details:
   | SIC Code							| No of Employees	| Legal entity Type		| Business Description	|
   | allow people to eat	| 10 to 49				| Limited Company			| Test Business					| 
  Then the second part of the partnership application is successfully completed
  
  #verify update of newly created partnership
  Given the user is on the PAR login page
  And the user logs in with the "par_authority@example.com" user credentials
  When the user searches for the last created partnership
  And the user updates the partnership information with the following info: "Updated Partnership info"
  Then the partnership is updated correctly
  
 @regression @partnershipapplication @coordinated
 Scenario: Verify Coordinated Partnership application by authority and completion by new business (Happy Path - PAR-1838, PAR-1839, PAR-1840, PAR-1841)
  Given the user is on the PAR home page
  And the user visits the login page
  And the user logs in with the "par_authority@example.com" user credentials
  Then the user is on the dashboard page
  When the user creates a new "Co-ordinated" partnership application with the following details:
   | Authority							| Partnership Info			| firstname	 	| lastname	| phone		| addressline1	| town		| postcode	|
   | Upper                	| Test partnership info | Test First	| test last	| 9797878	| 32 Bramtom Rd	| Windsor	| SL4 5PN		|
  Then the first part of the partnership application is successfully completed
  
  #second part of partnership application 
  When the user searches for the last created partnership
  And the user completes the partnership application with the following details:
   | SIC Code							| Member List Size	| Legal entity Type		| Business Description	|
   | allow people to eat	| Medium						| Limited Company			| Test Business					| 
  Then the second part of the partnership application is successfully completed
  
  #Given the user is on the PAR login page
  #And the user logs in with the "par_authority@example.com" user credentials
  #When the user searches for the last created partnership
  
 @regression @usermanagement
 Scenario: Verify Completion of User Creation journey (Happy Path - PAR-1904)
  Given the user is on the PAR login page
  And the user logs in with the "par_admin@example.com" user credentials
  When the user visits the maillog page and extracts the invite link
  And the user is on the PAR login page
  And the user follows the invitation link
  And the user completes the user creation journey
  Then the user journey creation is successful
 
 @regression @usermanagement @login @enforcement
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
   | Authority Type		| ONS Code	 | Regulatory Function	| addressline1	| town		| postcode	|
   | Council Area    	| 43453465	 | Cookie control				| 32 Bramtom Rd	| Windsor	| SL4 5PN		|  
  Then the authority is created sucessfully
  
  # Update All Fields for newly created Authority
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
 Scenario: Verify Send Notification of Proposed Enforcement (Happy Path - PAR-1852)
  Given the user is on the PAR login page
  And the user logs in with the "par_enforcement_officer@example.com" user credentials
  When the user searches for the last created partnership
  And the user creates an enforcement notice against the partnership with the following details:
   | Enforcement Action	| Title	 							| Regulatory Function	| Description				| Attachment		| 
   | Proposed    				| Enforcement Title 1	| Cookie control			| Enforcement desc	| link.txt			|  
  Then all the fields for the enforcement are updated correctly
  
  
  
  
  