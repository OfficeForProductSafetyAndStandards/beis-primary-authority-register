Feature: Upload inspection plan
    As an authority member
    I should be able to upload inspection plans

    @inspection_plan_access @ci @inspection_plans
    Scenario: Upload-advice type business
        Given I am logged in as "par_helpdesk@example.com"
        And I navigate to an active partnership "Lower East Side Borough Council"
        When I open inspection plan add page
        And I upload the file "files/test.png" to field "#edit-files-upload"
        And I click on the button "#edit-upload"
        And I enter inspection plan title
        And I enter summary of inspection plan
        And I click save
        And I see inspection plan uploaded successfully

    @inspection_plan_access @ci @inspection_plans
    Scenario: Edit inspection plan
        Given I am logged in as "par_helpdesk@example.com"
        And I navigate to an active partnership "Lower East Side Borough Council"
        And I click "See all Inspection Plans"
        When I click on edit against an inspection plan
        Then I enter new inspection plan title
        And I enter new summary for an inspection plan
        And I click save
        And I see the inspection plan has updated successfully

    @inspection_plan_access @ci @inspection_plans
    Scenario: Revoke an inspection plan
        Given I am logged in as "par_helpdesk@example.com"
        And I navigate to an active partnership "Lower East Side Borough Council"
        And I click "See all Inspection Plans"
        When I click on revoke against an inspection plan
        When I enter the revoke reason "inspection plan is no longer valid."
        And I click save
        Then I should archive successfully
