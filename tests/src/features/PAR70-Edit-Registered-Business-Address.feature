@Bug @journey3 @Pending
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
        Given I open the url "/dv/business-partnerships/1/details/address/1"
        And I add "08960" to the inputfield "#edit-postcode"
        And I add "Mompou 3, 7B" to the inputfield "#edit-address-line1"
        And I add "Sant Just Desvern" to the inputfield "#edit-address-line2"
        # And I add "TestPassword" to the inputfield "#edit-address-line3"
        And I add "Barcelona" to the inputfield "#edit-town-city"
        And I add "Desvern" to the inputfield "#edit-county"
        And I add "Spain" to the inputfield "#edit-country"
        When I click on the button "#edit-next"
        Then the element "#edit-registered-address" contains the text "08960"
        And the element "#edit-registered-address" contains the text "Mompou 3, 7B"
        And the element "#edit-registered-address" contains the text "Sant Just Desvern"
        And the element "#edit-registered-address" contains the text "Barcelona"
        And the element "#edit-registered-address" contains the text "Desvern"
        And the element "#edit-registered-address" contains the text "Spain"
