Feature: Inspection plan access
    As an enforcement officer
    I should be able to upload inspection plans

    @upload-advice @ci
    Scenario: Upload inspection plan disabled for enforcement offices
        Given I am logged in as "par_enforcement_officer@example.com"
        And I search for an active partnership "Enquirer"
        And I click "See all Inspection Plans"
        Then I should see inspection plans list page
        And I should not see the link "Upload inspection plan"

