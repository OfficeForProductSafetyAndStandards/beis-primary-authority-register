Feature: Upload inspection plan
    As an authority member
    I should be able to upload, Edit and Revoke Inspection Plans

    @inspection_plan_features @ci @inspection_plans
    Scenario: Upload-inspection plan
        Given I am logged in as "par_helpdesk@example.com"
        And I go to manage the partnership "Partnership nominated by Secretary of State" click on "Upper West Side Borough Council" and status "confirmed_rd"
        When I open inspection plan add page
        Then the element "h1.heading-xlarge" contains the text "Upload inspection plan documents"
        When I upload the file "files/test.png" to field "#edit-inspection-plan-files-upload"
        And I click on the button "#edit-upload"
        Then the element "h1.heading-xlarge" contains the text "Add inspection plan details"
        When I enter the inspection plan title "Auto-test-NewInspectionPlan"
        And I enter summary of inspection plan
        And I click save
        Then the element "h1.heading-xlarge" contains the text "When does this inspeciton plan expire?"
        And I add "14" to the inputfield "#edit-day"
        And I add "01" to the inputfield "#edit-month"
        And I add "2025" to the inputfield "#edit-year"
        And I click save
        And I see inspection plan uploaded successfully

    @inspection_plan_features @ci @inspection_plans
    Scenario: Edit inspection plan
        Given I am logged in as "par_helpdesk@example.com"
        And I go to manage the partnership "Partnership nominated by Secretary of State" click on "Upper West Side Borough Council" and status "confirmed_rd"
        And I click "See all Inspection Plans"
        When I edit the inspection plan "Auto-test-NewInspectionPlan"
        Then the element "h1.heading-xlarge" contains the text "Edit inspection plan details"
        When I enter the inspection plan title "Auto-test-NewInspectionPlan-retest"
        And I enter new summary for an inspection plan
        And I click save
        Then the element "h1.heading-xlarge" contains the text "Change the expiry date"
        And I add "28" to the inputfield "#edit-day"
        And I add "11" to the inputfield "#edit-month"
        And I add "2030" to the inputfield "#edit-year"
        And I click save
        And I see the inspection plan has updated successfully

    @inspection_plan_features @ci @inspection_plans
    Scenario: Revoke an inspection plan
        Given I am logged in as "par_helpdesk@example.com"
        And I go to manage the partnership "Partnership nominated by Secretary of State" click on "Upper West Side Borough Council" and status "confirmed_rd"
        And I click "See all Inspection Plans"
        When I revoke the inspection plan "Auto-test-NewInspectionPlan-retest"
        And I enter the revoke reason "inspection plan is no longer valid."
        And I click save
        Then I should revoke successfully
