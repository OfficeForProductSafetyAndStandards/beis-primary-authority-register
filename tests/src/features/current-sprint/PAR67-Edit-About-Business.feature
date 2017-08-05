@ci @journey3
Feature: As the (coordinated) Business User,
    I need to be able to see landing page for my co-ordinated Partnership,
    so that I can access the tasks required of me.

    Background:
        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario:
        Given I open the url "/dv/business-partnerships/1/details/about"
        And I add "Change to the about business details section" to the inputfield "#edit-about-business"
        When I click on the button "#edit-next"
        Then the element "#edit-about-business" contains the text "Change to the about business details section"
