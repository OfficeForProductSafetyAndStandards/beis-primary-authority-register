@Pending @journey3
Feature: As the (coordinated) Business User,
    I need to be able to see landing page for my co-ordinated Partnership,
    so that I can access the tasks required of me.

    Background:
        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: Manage business related addresses
        When I click on the button ".button-start"
        # PARTNERSHIPS DASHBOARD
        And I click on the link "ABCD Mart"
        # TERMS AND CONDITIONS SCREEN
        And I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"
        Then the element "h1" contains the text "Main contact at the Authority"
        When I click on the link "Review and confirm your partnership details"
        Then the element "#par-flow-transition-partnership-details-overview" contains the text "About the Partnership"
        And the element "#par-flow-transition-partnership-details-overview" contains the text "Main Primary Authority contact"
        And the element "#par-flow-transition-partnership-details-overview" contains the text "Main Business contact"
        And the element "#par-flow-transition-partnership-details-overview" contains the text "Areas of Regulatory Advice"
        When I click on the link "a.flow-link:first-child()"
        And I add "Change to the about business details section" to the inputfield "#edit-about-business"
        And I click on the button "#edit-next"
        Then the element "#edit-about-business" contains the text "Change to the about business details section"
        When I click on the button "html.js body.js-enabled main#content div div#block-par-theme-content form#par-flow-transition-business-details.par-flow-transition-business-details div fieldset#edit-0.js-form-item.form-item.js-form-wrapper.form-wrapper.inline em.placeholder a.flow-link"
        And I add "Trading Name Change" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-next"
        Then the element "#par-flow-transition-business-details" contains the text "Trading Name Change"
        When I click on the link "add another trading name"
        And I add "Trading Name Add" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-next"
        Then the element "#par-flow-transition-business-details" contains the text "Trading Name Add"