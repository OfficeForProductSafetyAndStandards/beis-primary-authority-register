Feature:
  As a user of the PAR service
  I  want to be able to view/manage partnerships
  So I can comply with the BEIS standards for goods and services
  
 @regression @partnershipapplication @direct
 Scenario: Verify New Partnership application by authority and completion by new business (Happy Path - PAR-1826.PAR-1835, PAR-1836, PAR-1837)
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
  
 @regression @partnershipapplication @coordinated
 Scenario: Verify New Partnership application by authority and completion by new business (Happy Path - PAR-1838, PAR-1839, PAR-1840, PAR-1841)
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
  
 @regression @partnershipapplication
 Scenario: Verify Completion of Partnership application with new business (Happy Path - PAR-1982)
  Given the user is on the PAR login page
  And the user logs in with the "par_authority@example.com" user credentials
  When the user searches for the last created partnership
  

  