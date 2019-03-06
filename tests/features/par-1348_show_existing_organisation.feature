Feature: Show existing organisation when creating new parternship

@par-1348 @data
Scenario Outline:Verify existing organisation is listed when creating a direct partnership
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

@par-1348 @data
Scenario Outline:Verify existing organisation is listed when creating a coordinated partnership
Given I am logged in as "par_authority@example.com"
When I click on "Apply for a new partnership"
And I select coordinated partnership
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




