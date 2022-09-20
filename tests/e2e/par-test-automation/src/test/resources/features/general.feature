Feature:
  As a user of the PAR service
  I  want to be able to view/manage partnerships
  So I can comply with the BEIS standards for goods and services
  
 @smoke
 Scenario: Verify login journey (Happy Path)
  Given the user is on the PAR home page
  And the user wants to login
  And the user logs in with the "parAdmin" user credentials
  Then the user is on the dashboard page

  