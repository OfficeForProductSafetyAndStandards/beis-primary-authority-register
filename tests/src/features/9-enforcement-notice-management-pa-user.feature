@ci @Bug
Feature: Enforcement notice management

#        When all the actions on an enforcement notice are undecided
#        And I go to the See enforcement notifications page from my dashboard
#        Then I should NOT see enforcement notice listed

    Scenario: Enforcement notice management

        Given I am logged in as "par_authority@example.com"
        When I click on the link "See enforcement notifications"
        Then the element "#views-exposed-form-par-user-enforcements-enforcement-notices-page" not contains the text "Title of enforcement notice"
        And I select the option with the text "Desc" for element "#edit-sort-order"
        And I click on the button "#edit-submit-par-user-enforcements"

#        # APPROVAL FORM

        And I click on the link "Title of enforcement notice Four"
        And I click on the radio "#edit-actions-0-primary-authority-status-approved"
        And I click on the button "#edit-actions-next"
        Then I expect that element "h1" contains the text "Confirm Enforcement Notice"
        And I click on the button "#edit-actions-next"
        Then the element "#views-exposed-form-par-user-enforcements-enforcement-notices-page" contains the text "Title of enforcement notice Four"
#       Then I expect that element "#par-rd-help-desk-approve" contains the text "Enforcement allowed"

#        # BLOCK FORM
#
        And I click on the link "Title of enforcement notice Three"
        And I click on the radio "#edit-actions-0-primary-authority-status-blocked"
        And I click on the button "#edit-actions-next"
        Then I expect that element "h1" contains the text "Confirm Enforcement Notice"
        And I click on the button "#edit-actions-next"
#        When I click on the button "a*=Dashboard"
#        When I click on the link "See enforcement notifications"
        Then the element "#views-exposed-form-par-user-enforcements-enforcement-notices-page" contains the text "Title of enforcement notice Three"
#       Then I expect that element "#par-rd-help-desk-approve" contains the text "Enforcement blocked"
#
#        # REFER FORM
#
#        When I click on the button "a*=Dashboard"
#        And I click on the link "See enforcement notifications"
        And I select the option with the text "Desc" for element "#edit-sort-order"
        And I click on the button "#edit-submit-par-user-enforcements"
        And I click on the link "Title of enforcement notice One"
        And I click on the radio "#edit-actions-0-primary-authority-status-referred"
        When I add "Some notes about why enforcement action referred" to the inputfield "#edit-actions-0-referral-notes"
        And I click on the button "#edit-actions-next"
        And I click on the radio ".option*=Upper West Side Borough Council"
        And I click on the button "#edit-actions-next"
        Then I expect that element "h1" contains the text "Confirm Enforcement Notice"
        And I click on the button "#edit-actions-next"
#        When I click on the button "a*=Dashboard"
#        And I click on the link "See enforcement notifications"
        Then the element "#views-exposed-form-par-user-enforcements-enforcement-notices-page" not contains the text "Title of enforcement notice One"


#    Scenario 1
#    As a member of the enforcing authority
#        When all the actions on an enforcement notice have been decided
#        And I go to the See enforcement notifications page from my dashboard
#        Then I should see enforcement notice listed which have been blocked or allowed
#    Scenario 2
#    As a member of the enforcing authority
#        When all the actions on an enforcement notice have been referred
#        And I go to the See enforcement notifications page from my dashboard
#        Then I should NOT see enforcement notice listed
