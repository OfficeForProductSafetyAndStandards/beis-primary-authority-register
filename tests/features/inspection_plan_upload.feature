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
