Feature: PA User - Update a partnership

    @ci2 @directpartnership
    Scenario: PA User - Update a partnership

        #LOGIN
        
        Given I am logged in as "par_authority@example.com"
            
        # GO TO A PARTNERSHIP PAGE
        
        When I click the link text "See your partnerships"
        And I add "Charlie" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I click the link text "Upper West Side Borough Council"
       
        # # EDIT ABOUT THE ORGANISATION

        # And I edit about the partnership

        # # EDIT MAIN AUTHORITY CONTACT

        # And I edit the main authority contact

        # ADVICE DOCUMENTATION

        And I upload a file to the partnership advice section

        # COMPLETE CHANGES

        And I click on the checkbox "#edit-partnership-info-agreed-authority"
        And I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Your partnerships"
