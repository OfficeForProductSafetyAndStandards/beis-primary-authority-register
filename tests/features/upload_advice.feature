Feature: Upload advice
  As an authority member
  I should be able to upload advice

  @upload-advice @ci
  Scenario: Verify upload advice is not available for non-active partnerships
    Given I am logged in as "par_authority@example.com"
    And I navigate to a partially completed partnership "Upper West Side Borough Council"
    And I click "See all Advice"
    Then I should see advice list page
    And I should not see the link "Upload advice"

  @upload-advice @ci
  Scenario: Verify upload advice is available for active partnerships only
    Given I am logged in as "par_authority@example.com"
    And I navigate to an active partnership "Upper West Side Borough Council"
    And I click "See all Advice"
    Then I should see advice list page
    And I see the link "Upload advice"

  @upload-advice @ci
  Scenario: Upload-advice type business
    Given I am logged in as "par_authority@example.com"
    And I navigate to an active partnership "Lower East Side Borough Council"
    When I open advice add page
    And I upload the file "files/test.png" to field "#edit-files-upload"
    And I click on the button "#edit-upload"
    And I enter the advice title "Auto-test-NewAdvice"
    And I enter summary of advice
    And I select advice type "business-advice"
    And I select "Cookie control" regulatory function
    And I click save
    Then I see that the advice "Auto-test-NewAdvice" uploaded successfully

  @upload-advice @ci
  Scenario: Upload-advice type background information
    Given I am logged in as "par_authority@example.com"
    And I navigate to an active partnership "Lower East Side Borough Council"
    When I open advice add page
    And I upload the file "files/test.png" to field "#edit-files-upload"
    And I click on the button "#edit-upload"
    And I enter the advice title "Background information for the business"
    And I enter summary of advice
    And I select advice type "background-information"
    And I select "Cookie control" regulatory function
    And I click save
    Then I see that the advice "Background information for the business" uploaded successfully

  @upload-advice
  Scenario: Helpdesk user able to upload advice for an active partnership
    Given I am logged in as "par_helpdesk@example.com"
    When I click the link text "Manage partnerships"
    And I add "Lower East Side Borough Council" to the inputfield "#edit-keywords"
    And I select the option with the value "confirmed_rd" for element "#edit-partnership-status"
    And I click on the button "#edit-submit-helpdesk-dashboard"
    Then I click the link text "Lower East Side Borough Council"
    And I open advice add page
    And I upload the file "files/test.png" to field "#edit-files-upload"
    And I click on the button "#edit-upload"
    And I enter the advice title "Environmental Health advice"
    And I enter summary of advice
    And I select advice type "business-advice"
    And I select "Environmental health" regulatory function
    And I click save
    Then I see that the advice "Environmental Health advice" uploaded successfully

  @upload-advice @ci
  Scenario: Edit advice
    Given I am logged in as "par_authority@example.com"
    And I navigate to an active partnership "Lower East Side Borough Council"
    And I click "See all Advice"
    When I click on edit against an advice
    Then I enter new advice title
    And I enter new summary of advice
    And I click save
    And I see advice updated successfully


  @upload-advice @ci
  Scenario: Archive advice
    Given I am logged in as "par_authority@example.com"
    And I navigate to an active partnership "Lower East Side Borough Council"
    And I click "See all Advice"
    When I click on archive against an advice
    And I click save
    Then I should archive successfully


  @upload-advice @ci
  Scenario: Remove advice
    Given I am logged in as "par_helpdesk@example.com"
    When I click the link text "Manage partnerships"
    And I add "Lower East Side Borough Council" to the inputfield "#edit-keywords"
    And I select the option with the value "confirmed_rd" for element "#edit-partnership-status"
    And I click on the button "#edit-submit-helpdesk-dashboard"
    Then I click the link text "Lower East Side Borough Council"
    And I click "See all Advice"
    When I click to remove the advice "Environmental Health advice"
    And I click continue
    Then I should not see the removed advice "Environmental Health advice"



