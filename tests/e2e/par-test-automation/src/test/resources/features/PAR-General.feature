Feature:
  As a user of the PAR service
  I  want to be able to view/manage partnerships
  So I can comply with the BEIS standards for goods and services
  
 @regression @partnershipapplication 
 Scenario: Verify New Partnership application with new business (Happy Path - PAR-1975)
  Given the user is on the PAR home page
  And the user visits the login page
  And the user logs in with the "par_authority@example.com" user credentials
  Then the user is on the dashboard page
  When the user creates a new "Direct" partnership application with the following details:
   | Authority								| Partnership Info	| firstname	 	| lastname	| phone		| addressline1	| town		| postcode	|
   | City Enformcement Squad	| Test							| Test First	| test last	| 9797878	| 32 Bramtom Rd	| Windsor	| SL4 5PN		|
  Then the partnership application is successfully created

  