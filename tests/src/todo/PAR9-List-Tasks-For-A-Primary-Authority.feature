@Pending @journey1
Feature: As a Primary Authority Officer
    I need to be able to see, and select from, a list of tasks for each Partnership
    so that I can carry out the tasks required for Data Validation.

    Background:
        Given I open the url "/login"
        And I add "PrimaryAuthority" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Thank you for registering"

    Scenario: Create New Partnership
        Given I press "Continue"
        And I click on the checkbox "#toc"
        And I press "Continue"
        Then I expect that element "h1" contains the text "You need to review and confirm the following partnerships"
        And I expect that element "#your-partnerships" does exist
        And I expect that element "#partnership-status" does exist
        When I click on the radio "#partnership-1"
        And I press "Continue"

#        When I click on the checkbox "#partnership-arrangement-confirm"
#        And I press "Continue"
#        And I click on the checkbox "#invite-business-to-confirm-their-details"
#        And I press "Continue"
#        Then I expect that element "#message-subject" contains the text "Important updates to the Primary Authority Register"
#        When I press "Send invite"
#        Then I expect that checkbox "#invite-business-to-confirm-their-details" is checked

#        When I click on the radio "#review-and-confirm-your-partnership-details"
#        And I press "Continue"
#        Then I expect that element "#review-and-confirm-partnership-details" contains the text "Review and confirm your partnership information"
#        And I expect that element "#areas-of-regulatory-advice" does exist
#
#        When I click on the link "#about-edit"
#        Then I expect that element "#partnership-information-edit-title" does exist
#        When I click on the link "#cancel-edit"
#
#        And I click on the link "#main-primary-authority-contact-edit"
#        Then I expect that element "#main-primary-authority-contact-edit-title" does exist
#        When I click on the link "#cancel-edit"
#
#        And I click on the link "#second-primary-authority-contact-edit"
#        Then I expect that element "#second-primary-authority-contact-edit-title" does exist
#        When I click on the link "#cancel-edit"
#
#
#        And I click on the checkbox "#confirm-partnership-information-correct"
#        And I press "Continue"
#        Then I expect that checkbox "#review-and-confirm-partnership-details" is checked
#
#        When I click on the radio "#review-and-confirm-your-inspection-plan"
#        And I press "Continue"
#        And I click on the checkbox "#review-and-confirm-inspection-plan"
#        And I press "Confirm"
#        Then I expect that checkbox "#review-and-confirm-your-inspection-plan" is checked
#
#        And I click on the checkbox "#review-and-confirm-your-documentation"
#        And I press "Continue"
#        And I click on the checkbox "#select-all-documents"
#        And I press "Confirm"
#
#        Then I expect that element "#success-header" contains the text "You have successfully confirmed all your Primary Authority information for your partnership"
#        And I press "Done"
