Feature: PA User - Update a partnership

    @ci @directpartnership
    Scenario: PA User - Update a partnership

        # APPROVE COORDINATED PARTNERSHIP (HELPDESK)

        Given I open the path "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I add "Organisation For Coordinated Partnership" to the inputfield "#edit-keywords"
        And I select the option with the value "confirmed_business" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I click the link text "Approve partnership"
        And the element "#par-rd-help-desk-confirm" contains the text "Organisation For Coordinated Partnership"
        And I click on the radio "#edit-confirm-authorisation-select"
        And I click on the radio "#edit-partnership-regulatory-functions-1"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge" contains the text "Partnership is approved"
        And the element "#edit-partnership-info" contains the text "Organisation For Coordinated Partnership"
        And I click on the button "#edit-done"

        And I open the path "/user/logout"

        #LOGIN
        
        Given I am logged in as "par_authority@example.com"
            
        # GO TO A PARTNERSHIP PAGE
        
        And I go to detail page for partnership with authority "City Enforcement Squad"

        # EDIT ABOUT THE ORGANISATION

        When I edit about the partnership

        # EDIT MAIN AUTHORITY CONTACT

        And I edit the main authority contact

        # ADVICE DOCUMENTATION

        And I upload a file to the partnership advice section

        # COMPLETE CHANGES

        And I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Your partnerships"
