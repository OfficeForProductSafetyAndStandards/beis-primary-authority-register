@Pending
Feature: As a Primary Authority Officer
I want to be able to invite the business to confirm their details
so that the business information is up to date in the new PAR

    Background:
        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario:
        Given I open the urkl "/dv/primary-authority-partnerships/1/invite-business/1"
        Then the element "#toc-title" contains the text "I confirm that my business agrees to the new terms and conditions"


