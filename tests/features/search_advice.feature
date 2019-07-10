Feature: Search advice
  As an enforcement officer
  I should be able to search advice

  @upload-advice @ci
  Scenario: Search for advice on other partnerships
    Given I am logged in as "par_enforcement@example.com"
    And I search for an active partnership "Upper West Side Borough Council"
    And I click "See all Advice"
    Then I should see advice page
    And I should not see the link "Upload advice"
    When I search for active advice by the title "Cookie Quotas"




