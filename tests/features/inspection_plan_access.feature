Feature: Inspection plan access
    As an enforcement officer
    I should be able to upload inspection plans

    @inspection_plan_access @ci @inspection_plan
    Scenario: Upload inspection plan disabled for enforcement offices
        Given I am logged in as "par_enforcement_officer@example.com"
        And I search for an active partnership "Enquirer"
        And I click "See all Inspection Plans"
        Then I should see inspection plans list page
        And I should not see the link "Upload inspection plan"

    @inspection_plan_access @ci @inspection_plan
    Scenario: Upload inspection plan disabled for business users
        Given I am logged in as "par_business_enquirer@example.com"
        And I search for an active partnership "Enquirer"
        And I click "See all Inspection Plans"
        Then I should see inspection plans list page
        And I should not see the link "Upload inspection plan"

    Scenario: Upload inspection plan enabled for helpdesk/admin users
        Given I am logged in as "par_helpdesk@example.com"
        And I go to manage the partnership "Partnership nominated by Secretary of State" with status "confirmed_rd"
        And I click "See all Inspection Plans"
        Then I should see inspection plans list page
        And I see the inspection plan link "Upload inspection plan"
