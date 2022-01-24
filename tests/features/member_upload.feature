Feature: Search advice
  As an enforcement officer
  I should be able to search advice

  @upload-advice @ci
  Scenario: Search for advice on other partnerships
    Given I am logged in as "par_enforcement_officer@example.com"
    And I search for an active partnership "Enquirer"
    And I click "See all Advice"
    Then I should see advice list page
    And I should not see the link "Upload advice"
    When I search for active advice by the title "Cookie Quotas"
    Then I should see advice view page has the title "Cookie Quotas"

  Scenario: Search for advice on other partnerships
    Given I am logged in as "par_business_enquirer@example.com"
    And I go to partnership detail page for my partnership "Enquirer" with status "confirmed_rd"
    And I click "See all Advice"
    Then I should see advice list page
    And I should not see the link "Upload advice"
    When I search for active advice by the title "Cookie Quotas"
    Then I should see advice view page has the title "Cookie Quotas"

  Scenario: Search for advice on other partnerships
    Given I am logged in as "par_authority@example.com"
    When I open an active partnership "Lower East Side Borough Council" by searching for "Enquirer"
    And I click "See all Advice"
    Then I should see advice list page
    When I search for active advice by the title "Cookie Quotas"
    Then I should see advice view page has the title "Cookie Quotas"




