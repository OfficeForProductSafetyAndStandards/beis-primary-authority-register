Feature: Show existing organisation when creating new parternship

Scenario:Verify existing organisation is listed
Given I am logged in as "par_authority_profile@example.com"
When I click on "Apply for a new partnership"
And I select direct partnership
And I fill in all required fields
And I enter existing organistaion "Asda"
Then I should be able to see Asda 


Scenario: Select existing organisation 
When I select existing organisation "Asda"
Then I should be able to complete part
