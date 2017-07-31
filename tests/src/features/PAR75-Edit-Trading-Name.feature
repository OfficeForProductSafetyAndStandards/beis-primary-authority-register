@ci @journey3
Feature: As a Business User	
I need to be able to edit the field 'Trading Name' in the existing business details 	
So that the correct details are taken forward into the new PAR 

    Background:
        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible


    Scenario: Edit Trading Name
        Given I click on the link "ABCD Mart"
        And I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"
        And I click on the link "Review and confirm your business details"
        And I open the url "/dv/business-partnerships/4/details/trading-name/0"
        # And I scroll to element "//body/main/div[2]/div[4]/form/div[6]/fieldset/em/a"
        # And I press "a.flow-link:last"
        And I add "Trading Name Change" to the inputfield "#edit-trading-name"
        When I click on the button "#edit-next"
        Then the element "#edit-0" contains the text "Trading Name Change"
 