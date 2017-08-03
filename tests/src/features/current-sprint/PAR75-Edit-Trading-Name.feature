@ci @journey3
Feature: As a Business User
    I need to be able to edit the field 'Trading Name' in the existing business details
    So that the correct details are taken forward into the new PAR

    Background:
        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I expect that element ".error-message" is not visible
        And I click on the button ".button-start"

    Scenario: Edit Trading Name
        Given I click on the link "ABCD Mart"
        And I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"
        And I click on the link "Review and confirm your business details"
        # And I scroll to element "//body/main/div[2]/div[4]/form/div[6]/fieldset/em/a"
        # And I press "a.flow-link:last"
        And I click on the button "html.js body.js-enabled main#content div div#block-par-theme-content form#par-flow-transition-business-details.par-flow-transition-business-details div fieldset#edit-0.js-form-item.form-item.js-form-wrapper.form-wrapper.inline em.placeholder a.flow-link"
        And I add "Trading Name Change" to the inputfield "#edit-trading-name"
        When I click on the button "#edit-next"
        Then the element "#par-flow-transition-business-details" contains the text "Trading Name Change"
        And I click on the link "add another trading name"
        And I add "Trading Name Add" to the inputfield "#edit-trading-name"
        When I click on the button "edit-next"
        Then the element "#par-flow-transition-business-details" contains the text "Trading Name Add"
