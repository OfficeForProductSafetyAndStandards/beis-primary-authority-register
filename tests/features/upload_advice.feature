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
    And I enter advice title
    And I enter summary of advice
    And I select advice type "business-advice"
    And I select "Cookie control" regulatory function
    And I click save
    And I see advice uploaded successfully

  @upload-advice @ci
  Scenario: Upload-advice type background information
    Given I am logged in as "par_authority@example.com"
    And I navigate to an active partnership "Lower East Side Borough Council"
    When I open advice add page
    And I upload the file "files/test.png" to field "#edit-files-upload"
    And I click on the button "#edit-upload"
    And I enter advice title
    And I enter summary of advice
    And I select advice type "background-information"
    And I select "Cookie control" regulatory function
    And I click save
    And I see advice uploaded successfully

  @upload-advice
  Scenario: Helpdesk user able to upload advice for an active partnership
    Given I am logged in as "par_helpdesk@example.com"
    And I navigate to an active partnership "Lower East Side Borough Council"
    When I open advice add page
    And I upload the file "files/test.png" to field "#edit-files-upload"
    And I click on the button "#edit-upload"
    And I enter advice title
    And I enter summary of advice
    And I select advice type "business-advice"
    And I select "Environmental health" regulatory function
    And I click save
    And I see advice uploaded successfully

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
    When I enter reason "new advice is ready"
    And I click save
    Then I should archive successfully


  Scenario: validate error when guidline checkbox is not selected

  Scenario: valdiate error message when title, summary or type of advice missing

  Scenario: Verify search advice

  Scenario: Verify filter advice




