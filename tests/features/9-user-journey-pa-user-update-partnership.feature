@ci
Feature: PA User - Update a partnership

    Scenario: PA User - Update a partnership

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
