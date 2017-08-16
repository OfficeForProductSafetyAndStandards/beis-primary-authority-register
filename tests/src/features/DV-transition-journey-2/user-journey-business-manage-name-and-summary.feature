@ci @journey2
Feature: Business User - Manage name and summary detail

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Business User - Manage name and summary detail
        # LOGIN SCREEN

        Given I am logged in as "par_business@example.com"
        When I click on the button ".button-start"

        # PARTNERSHIPS DASHBOARD

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "ABCD Mart"

        # TERMS AND CONDITIONS SCREEN

        Then I expect that element ".par-flow-transition-business-terms" contains the text "Please Review the new Primary Authority terms and conditions and confirm that you agree with them"
        When I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"

        # PARTNERSHIP TASKS

        Then I expect that element "h3" contains the text "Main contact at the Authority"
        When I click on the link "Review and confirm your business details"

        # REVIEW BUSINESS DETAILS

        Then I expect that element "#par-flow-transition-business-details" contains the text "About the business"
        And I expect that element "#par-flow-transition-business-details" contains the text "Registered address"
        And I expect that element "#par-flow-transition-business-details" contains the text "Legal Entities"
        And I expect that element "#par-flow-transition-business-details" contains the text "Trading Names"
        When I click on the link "edit"
        And I add "Change to the about business details section" to the inputfield "#edit-about-business"
        And I click on the button "#edit-next"
        Then I expect that element "#edit-about-business" contains the text "Change to the about business details section"
        When I click on the button "form#par-flow-transition-business-details #edit-0.js-form-item a.flow-link"
        And I add "Trading Name Change" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-next"
        Then I expect that element "#par-flow-transition-business-details" contains the text "Trading Name Change"
        When I click on the link "add another trading name"
        And I click on the button "#edit-next"
        And I add "Trading Name Add" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-next"
        Then I expect that element "#par-flow-transition-business-details" contains the text "Trading Name Add"
        And I click on the checkbox "#edit-confirmation"
        And I click on the button "#edit-next"

        # PARTNERSHIP TASKS

        Then I expect that element "#block-par-theme-content" contains the text "Confirmed by the Organisation"
        And I expect that element ".table-scroll-wrapper" not contains the text "Invite the business to confirm their details"
        And I expect that element ".table-scroll-wrapper" not contains the text "Review and confirm your inspection plan"
        And I expect that element ".table-scroll-wrapper" not contains the text "Review and confirm your documentation"



