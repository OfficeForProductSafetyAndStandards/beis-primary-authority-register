Feature: Coordinator User - Update Partnership

    @coordinatedpartnership @ci @PAR790
    Scenario: Coordinator User - Update Partnership

        #LOGIN
        
        Given I am logged in as "par_coordinator@example.com"
        When I click the link with text "See your partnerships"
        And I add "Organisation For Coordinated Partnership" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I click the link text "Organisation For Coordinated Partnership"
        Then the element "h1" is not empty

        # ADD MEMBERS

        When I click the link text "Add a member"
        And I add "New Member One" to the inputfield "#edit-name"
        And I click on the button "#edit-next"
        And I add "MK43 7AS" to the inputfield "#edit-postcode"
        And I add "1 High St" to the inputfield "#edit-address-line1"
        And I add "New Change" to the inputfield "#edit-address-line2"
        And I add "Odell" to the inputfield "#edit-town-city"
        And I add "Bedfordshire" to the inputfield "#edit-county"
        And I select the option with the value "GB" for element "#edit-country-code"
        And I select the option with the value "GB-ENG" for element "#edit-nation"
        And I click on the button "#edit-next"
        And I add "Mr" to the inputfield "#edit-salutation"
        And I add "MemberContact" to the inputfield "#edit-last-name"
        And I add "02089009000" to the inputfield "#edit-work-phone"
        And I add "07845333448" to the inputfield "#edit-mobile-phone"
        And I add "add.membercontact@example.com" to the inputfield "#edit-email"
        And I add "Add" to the inputfield "#edit-first-name"
        And I click on the button "#edit-next"
        And I add "14" to the inputfield "#edit-day"
        And I add "01" to the inputfield "#edit-month"
        And I add "2018" to the inputfield "#edit-year"
        And I click on the button "#edit-next"
        And I add "A trading Name" to the inputfield "#edit-par-component-trading-name-0-trading-name"
        And I click on the button "#edit-next"
        And I add "New LLP Company" to the inputfield "#edit-par-component-legal-entity-0-registered-name"
        And I select the option with the value "limited_liability_partnership" for element "#edit-par-component-legal-entity-0-legal-entity-type"
        When I add "1234567890" to the inputfield "#edit-par-component-legal-entity-0-registered-number"
        And I click on the button "#edit-next"
        And I click on the button "#edit-next"
        Then the element "#block-par-theme-content" contains the text "New Member One"
        And the element "#block-par-theme-content" contains the text "MK43 7AS"
        And the element "#block-par-theme-content" contains the text "1 High St"
        And the element "#block-par-theme-content" contains the text "Odell"
        And the element "#block-par-theme-content" contains the text "United Kingdom"
        And the element "#block-par-theme-content" contains the text "14 January 2018"
        And the element "#block-par-theme-content" contains the text "A trading Name"
        And the element "#block-par-theme-content" contains the text "New LLP Company"
        And the element "#block-par-theme-content" contains the text "Limited Liability Partnership"
        And the element "#block-par-theme-content" contains the text "14 January 2018"
        When I click on the button "#edit-save"
        And the element "h1.heading-xlarge" contains the text "Member added"
        When I click on the button ".button"    
        # And I run tota11y against the current page
        And the element "h1.heading-xlarge" contains the text "Members list"
        And the element ".table-scroll-wrapper" contains the text "New Member One"
        And the element ".table-scroll-wrapper" contains the text "14 January 2018"

        # SEARCH MEMBERS

        And I add "New Member Two" to the inputfield "#edit-organisation-name"
        And I click on the button "#edit-submit-members-list"
        And the element "body" does not contain the text "New Member Two"
        And I add "New Member One" to the inputfield "#edit-organisation-name"
        When I click on the button "#edit-submit-members-list"
        Then the element ".table-scroll-wrapper" contains the text "New Member One"
        When I select the option with the value "2" for element "#edit-revoked"
        And I click on the button "#edit-submit-members-list"
        Then the element ".table-scroll-wrapper" does not exist
        When I select the option with the value "1" for element "#edit-revoked"
        And I click on the button "#edit-submit-members-list"
        Then the element ".table-scroll-wrapper" contains the text "New Member One"

        # INSPECTION PLAN STATUS
        
        When I click the link text "New Member One"
        And I click the link text "edit covered by inspection plan"
        And I click on the radio "#edit-covered-by-inspection-1"
        And I click on the button "#edit-save"
        And the element "#edit-covered-by-inspection" contains the text "Yes"
        And I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Members list"

        # CEASE MEMBER

        When I click the link text "Cease membership"
        # And I run tota11y against the current page
        And I add "20" to the inputfield "#edit-day"
        And I add "2" to the inputfield "#edit-month"
        And I add "2018" to the inputfield "#edit-year"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge" contains the text "Membership Ceased"
        And I click on the button "#edit-save"
        Then the element "a*=New Member One" does not exist
        Then the element "Cease membership" does not exist
        And the element "td.views-field.views-field-date-membership-ceased" contains the text "20 February 2018"
        And I click the link text "◀ Back to partnership"
       
        # EDIT REGISTERED ADDRESS

        When  I click the link text "edit address"
        And I add "SE16 4NX" to the inputfield "#edit-postcode"
        And I add "1 Change St" to the inputfield "#edit-address-line1"
        And I add "New Change" to the inputfield "#edit-address-line2"
        And I add "London" to the inputfield "#edit-town-city"
        And I add "London" to the inputfield "#edit-county"
        And I select the option with the value "GB" for element "#edit-country-code"
        And I select the option with the value "GB-ENG" for element "#edit-nation"
        When I click on the button "#edit-save"
        Then the element "#edit-registered-address" contains the text "1 Change St"
        And the element "#edit-registered-address" contains the text "New Change"
        And the element "#edit-registered-address" contains the text "London"
        And the element "#edit-registered-address" contains the text "SE16 4NX"

        # EDIT ABOUT THE ORGANISATION

        When I click the link text "edit about the organisation"
        And I add "Change to the about organisation details section" to the inputfield "#edit-about-business"
        And I click on the button "#edit-save"
        Then the element "#edit-about-business" contains the text "Change to the about organisation details section"

        # ADD NEW TRADING NAME

        When I click the link text "add another trading name"
        # And I run tota11y against the current page
        Then the element "h1.heading-xlarge" contains the text "Add a trading name for your organisation"
        When I add "Different Trading Name" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-save"
        Then the element "#edit-trading-names" contains the text "Different Trading Name"

        # EDIT TRADING NAME

        When I click the link text "edit trading name"
        # And I run tota11y against the current page
        Then the element "h1.heading-xlarge" contains the text "Edit trading name for your organisation"
        When I add "Different Trading Name (edited)" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-save"
        Then the element "#edit-trading-names" contains the text "Different Trading Name (edited)"

        # EDIT MAIN ORGANISATION CONTACT

        When I click the link text "edit organisation contact"
        # And I run tota11y against the current page
        And I add "Tim" to the inputfield "#edit-first-name"
        And I add "Whistler" to the inputfield "#edit-last-name"
        And I add "2079999999" to the inputfield "#edit-work-phone"
        And I add "78659999999" to the inputfield "#edit-mobile-phone"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-save"
        Then the element "#edit-organisation-contacts" contains the text "Tim"
        And the element "#edit-organisation-contacts" contains the text "Whistler"
        And the element "#edit-organisation-contacts" contains the text "2079999999"
        And the element "#edit-organisation-contacts" contains the text "78659999999"

        # COMPLETE CHANGES

        When I click on the button "#edit-save"
        # And I run tota11y against the current page
        And I add "Organisation For Coordinated Partnership" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"
        And the element "#block-par-theme-content" contains the text "Organisation For Coordinated Partnership"
