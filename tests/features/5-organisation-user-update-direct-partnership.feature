Feature: Business User - Update Partnership

    @ci @PAR990 @PAR991 @directpartnership
    Scenario: Business User - Update Partnership

        #LOGIN

        Given I am logged in as "par_business@example.com"

        # GO TO A PARTNERSHIP PAGE

        And I go to partnership detail page for my partnership "Organisation For Direct Partnership" with status "confirmed_business"
       
        # EDIT REGISTERED ADDRESS

        When I update the registered address for organisation

        # ADD SIC CODES

        And I update the SIC code

        # ADD EMPLOYEES

        And I update the number of employees

        # ADD NEW TRADING NAME

        And I add and subsequently edit a trading name

        # ADD ORGANISATION CONTACT

        And I add and subsequently edit a organisation contact

        # COMPLETE CHANGES

        When I click on the button "#edit-save"
        And I click on the button "#edit-submit-par-user-partnerships"
        And the element "#block-par-theme-content" contains the text "Organisation For Direct Partnership"
