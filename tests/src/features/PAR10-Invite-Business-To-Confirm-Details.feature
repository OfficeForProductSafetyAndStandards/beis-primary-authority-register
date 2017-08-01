@ci
Feature: As a Primary Authority Officer
I want to be able to invite the business to confirm their details
so that the business information is up to date in the new PAR

    Background:
        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario:
        Given I open the url "/dv/primary-authority-partnerships/1/details/invite/3"
        Then the element "h1" contains the text "Invite business to the Primary Authority Register"
        Then I expect that element "#edit-authority-member" is not enabled
        And I expect that element "#edit-business-member" is not enabled
        And I expect that element "#edit-email-subject" is enabled
        # When I click on the button "#edit-send"
        # Then the element "h1" contains the text "Invite business to the Primary Authority Register"
