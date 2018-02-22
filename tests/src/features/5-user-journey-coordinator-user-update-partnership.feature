@ci @PAR790
Feature: Coordinator User - Update Partnership

    Scenario: Coordinator User - Update Partnership

        # PARTNERSHIPS DASHBOARD
        Given I open the url "/user/login"
        And I add "par_coordinator@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element "#block-par-theme-content" contains the text "See your partnerships"
        And I click on the link "See your partnerships"
        And I click on the link "Organisation For Coordinated Partnership 20"
        Then I expect that element "h1" is not empty

        # ADD MEMBERS

        When I open the add members page
        And I add "New Member One" to the inputfield "#edit-organisation-name"
        And I click on the button "#edit-next"
        And I clear the inputfield "#edit-postcode"
        And I add "MK43 7AS" to the inputfield "#edit-postcode"
        And I add "1 High St" to the inputfield "#edit-address-line1"
        And I add "New Change" to the inputfield "#edit-address-line2"
        And I add "Odell" to the inputfield "#edit-town-city"
        And I add "Bedfordshire" to the inputfield "#edit-county"
        And I select the option with the text "United Kingdom" for element "#edit-country-code"
        And I select the option with the text "England" for element "#edit-nation"
        And I click on the button "#edit-next"
        And I clear the inputfield "#edit-first-name"
        And I add "Mr" to the inputfield "#edit-salutation"
        And I add "MemberContact" to the inputfield "#edit-last-name"
        And I add "02089009000" to the inputfield "#edit-work-phone"
        And I add "07845333444" to the inputfield "#edit-mobile-phone"
        And I add "add.membercontact@example.com" to the inputfield "#edit-email"
        And I click on the button "#edit-next"
        Then I expect that element ".error-summary" does exist
        And I add "Add" to the inputfield "#edit-first-name"
        And I click on the button "#edit-next"
        And I clear the inputfield "#edit-day"
        And I clear the inputfield "#edit-month"
        And I add "14" to the inputfield "#edit-day"
        And I add "01" to the inputfield "#edit-month"
        And I click on the button "#edit-next"
        And I add "A trading Name" to the inputfield "#edit-par-component-trading-name-0-trading-name"
        And I click on the button "#edit-next"
        And I add "New LLP Company" to the inputfield "#edit-par-component-legal-entity-0-registered-name"
        And I select the option with the text "Limited Liability Partnership" for element "#edit-par-component-legal-entity-0-legal-entity-type"
        When I add "1234567890" to the inputfield "#edit-par-component-legal-entity-0-registered-number"
        And I click on the button "#edit-next"
        And I click on the button "#edit-next"
        Then I expect that element "#block-par-theme-content" contains the text "New Member One"
        And I expect that element "#block-par-theme-content" contains the text "MK43 7AS"
        And I expect that element "#block-par-theme-content" contains the text "1 High St"
        And I expect that element "#block-par-theme-content" contains the text "Odell"
        And I expect that element "#block-par-theme-content" contains the text "United Kingdom"
        And I expect that element "#block-par-theme-content" contains the text "14 January 2018"
        And I expect that element "#block-par-theme-content" contains the text "A trading Name"
        And I expect that element "#block-par-theme-content" contains the text "New LLP Company"
        And I expect that element "#block-par-theme-content" contains the text "Limited Liability Partnership"
        And I expect that element "#block-par-theme-content" contains the text "14 January 2018"
        When I click on the button "#edit-save"
        And I expect that element "h1.heading-xlarge" contains the text "Member added"
        When I click on the button ".button"    
        And I expect that element "h1.heading-xlarge" contains the text "Members list"
        And I expect that element ".table-scroll-wrapper" contains the text "New Member One"
        And I expect that element ".table-scroll-wrapper" contains the text "14 January 2018"

        # SEARCH MEMBERS

        And I add "New Member Two" to the inputfield "#edit-organisation-name"
        And I click on the button "#edit-submit-members-list"
        And I expect that element ".table-scroll-wrapper" does not exist
        And I clear the inputfield "#edit-organisation-name"
        And I add "New Member One" to the inputfield "#edit-organisation-name"
        When I click on the button "#edit-submit-members-list"
        Then I expect that element ".table-scroll-wrapper" contains the text "New Member One"
        When I select the option with the text "Ceased" for element "#edit-revoked"
        And I click on the button "#edit-submit-members-list"
        Then I expect that element ".table-scroll-wrapper" does not exist
        When I select the option with the text "Current" for element "#edit-revoked"
        And I click on the button "#edit-submit-members-list"
        Then I expect that element ".table-scroll-wrapper" contains the text "New Member One"
       
        # EDIT REGISTERED ADDRESS

        Given I open the url "/partnerships"
        And I click on the link "Organisation For Coordinated Partnership 20"
        When  I click on the link "edit address"
        And I clear the inputfield "#edit-address-line1"
        And I clear the inputfield "#edit-address-line2"
        And I clear the inputfield "#edit-town-city"
        And I clear the inputfield "#edit-postcode"
        And I clear the inputfield "#edit-county"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" does exist
        When I add "SE16 4NX" to the inputfield "#edit-postcode"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" does exist
        And I add "1 Change St" to the inputfield "#edit-address-line1"
        And I add "New Change" to the inputfield "#edit-address-line2"
        And I add "London" to the inputfield "#edit-town-city"
        And I add "London" to the inputfield "#edit-county"
        And I select the option with the text "United Kingdom" for element "#edit-country-code"
        And I select the option with the text "England" for element "#edit-nation"
        When I click on the button "#edit-save"
        Then I expect that element "#edit-registered-address" contains the text "1 Change St"
        And I expect that element "#edit-registered-address" contains the text "New Change"
        And I expect that element "#edit-registered-address" contains the text "London"
        And I expect that element "#edit-registered-address" contains the text "SE16 4NX"

        # EDIT ABOUT THE BUSINESS

        When I click on the link "edit about the organisation"
        And I add "Change to the about organisation details section" to the inputfield "#edit-about-business"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-about-business" contains the text "Change to the about organisation details section"

        # ADD MEMBERS

        When I click on the link "edit number of members"
        And I select the option with the text "Small" for element "#edit-business-size"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-associations" contains the text "small"

        # ADD NEW TRADING NAME

        When I click on the link "add another trading name"
        Then I expect that element "h1.heading-xlarge" contains the text "Add a trading name for your organisation"
        When I add "Different Trading Name" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-trading-names" contains the text "Different Trading Name"

        # EDIT TRADING NAME

        When I click on the link "edit trading name"
        Then I expect that element "h1.heading-xlarge" contains the text "Edit trading name for your organisation"
        When I add "Different Trading Name (edited)" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-trading-names" contains the text "Different Trading Name (edited)"

        # EDIT MAIN BUSINESS CONTACT

        When I click on the link "edit organisation contact"
        And I add "Harvey" to the inputfield "#edit-first-name"
        And I add "Kneeslapper" to the inputfield "#edit-last-name"
        And I add "2079999999" to the inputfield "#edit-work-phone"
        And I add "78659999999" to the inputfield "#edit-mobile-phone"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-organisation-contacts" contains the text "Harvey"
        And I expect that element "#edit-organisation-contacts" contains the text "Kneeslapper"
        And I expect that element "#edit-organisation-contacts" contains the text "2079999999"
        And I expect that element "#edit-organisation-contacts" contains the text "78659999999"

        # COMPLETE CHANGES

        When I click on the button "#edit-save"
        And I select the option with the text "Confirmed by the Organisation" for element "#edit-partnership-status-1"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I expect that element "#block-par-theme-content" contains the text "Organisation For Coordinated Partnership 20"

