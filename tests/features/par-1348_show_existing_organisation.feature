Feature: Show existing organisation when creating new parternship

Scenario Outline:Verify existing organisation is listed
Given I am logged in as "par_authority@example.com"
When I click on "Apply for a new partnership"
And I select direct partnership
And I fill in all required fields
And I enter existing organistaion "<OrgName>"
Then I should be able to see "<OrgName>" 

#Scenario: Verfiy I should be able to complete parternship
When I select existing organisation "<OrgName>"
Then I should be able to complete partnership

Examples:
|OrgName			 |
|Asda				 |
|The Tile Association|



