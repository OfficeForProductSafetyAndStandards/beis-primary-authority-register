@ci @Bug @PAR990 @PAR991
Feature: Business User - Manage Addresses


    Scenario: Business User - Manage Addresses

        #LOGIN

        Given I am logged in as "par_business@example.com"
        And I open the path "/dashboard"
        And I click the link text "See your partnerships"
        # And I select the option with the value "Confirmed by Organisation" for element "#edit-partnership-status"
        And I add "Organisation For Direct Partnership" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I click the link text "Organisation For Direct Partnership"

        # EDIT REGISTERED ADDRESS

#         When  I click the link text "edit address"
#         When I clear the inputfield "#edit-postcode"
#         And I click on the button "#edit-save"
#         Then the element ".error-summary" is visible
#         When I add "SE16 4NX" to the inputfield "#edit-postcode"
#         And I add "1 Change St" to the inputfield "#edit-address-line1"
#         And I add "New Change" to the inputfield "#edit-address-line2"
#         When I add "London" to the inputfield "#edit-town-city"
#         When I add "London" to the inputfield "#edit-county"
#         And I select the option with the value "GB" for element "#edit-country-code"
#         And I select the option with the value "GB-ENG" for element "#edit-nation"
#         When I click on the button "#edit-save"
#         Then the element "#edit-registered-address" contains the text "1 Change St"
#         And the element "#edit-registered-address" contains the text "New Change"
#         And the element "#edit-registered-address" contains the text "London"
#         And the element "#edit-registered-address" contains the text "SE16 4NX"

# #        # EDIT ABOUT THE ORGANISATION

#         When I click the link text "edit about the organisation"
#         Then the element "h1.heading-xlarge .heading-secondary" contains the text "Primary Authority partnership information"
#         Then the element "h1.heading-xlarge" contains the text "Information about the organisation"
#         And I add "Change to the about organisation details section" to the inputfield "#edit-about-business"
#         And I click on the button "#edit-save"
#         Then the element "#edit-about-business" contains the text "Change to the about organisation details section"

#         # ADD SIC CODES

#         When I click the link text "add another sic code"
#         And I select the option with the value "38" for element "#edit-sic-code"
#         And I click on the button "#edit-save"
#         Then the element "#edit-sic-codes" contains the text "Social care activities without accommodation"

#         # ADD EMPLOYEES

#         When I click the link text "edit number of employees"
#         And I select the option with the value "250" for element "#edit-employees-band"
#         And I click on the button "#edit-save"
#         Then the element "#edit-employee-no" contains the text "50 to 249"

#         # # ADD LEGAL ENTITY

#         # When I click the link text "add another legal entity"
#         # Then the element "h1.heading-xlarge .heading-secondary" contains the text "Primary Authority partnership information"
#         # Then the element "h1.heading-xlarge" contains the text "Add a legal entity for your organisation"
#         # Then the element ".form-item-registered-name label" contains the text "Enter name of the legal entity"
#         # When I add "New LLP Company" to the inputfield "#edit-registered-name"
#         # And I select the option with the value "limited_liability_partnership" for element "#edit-legal-entity-type"
#         # When I add "1234567890" to the inputfield "#edit-registered-number"
#         # And I click on the button "#edit-save"
#         # Then the element "#edit-legal-entities" contains the text "New LLP Company"
#         # Then the element "#edit-legal-entities" contains the text "Limited Liability Partnership"
#         # Then the element "#edit-legal-entities" contains the text "1234567890"

#         # # EDIT LEGAL ENTITY

#         # When I click the link text "edit legal entity"
#         # And I clear the inputfield "#edit-registered-name"
#         # When I add "Changed" to the inputfield "#edit-registered-name"
#         # And I select the option with the value "Limited Company" for element "#edit-legal-entity-type"
#         # And I clear the inputfield "#edit-registered-number"
#         # When I add "0123456789" to the inputfield "#edit-registered-number"
#         # And I click on the button "#edit-save"
#         # Then the element "#edit-legal-entities" contains the text "Changed"
#         # Then the element "#edit-legal-entities" contains the text "Limited Company"
 
#         # # ADD ANOTHER LEGAL ENTITY - Sole Trader
 
#         # When I click the link text "add another legal entity"
#         # Then the element "h1.heading-xlarge .heading-secondary" contains the text "Primary Authority partnership information"
#         # Then the element "h1.heading-xlarge" contains the text "Add a legal entity for your organisation"
#         # When I add "New Sole Trader" to the inputfield "#edit-registered-name"
#         # And I select the option with the value "sole_trader" for element "#edit-legal-entity-type"
#         # Then the element ".form-item-registered-number" does not exist
#         # And I click on the button "#edit-save"
#         # Then the element "#edit-legal-entities" contains the text "New Sole Trader"
#         # Then the element "#edit-legal-entities" contains the text "Sole Trader"

#         # ADD NEW TRADING NAME

#         When I click the link text "add another trading name"
#         Then the element "h1.heading-xlarge" contains the text "Add a trading name for your organisation"
#         When I add "Different Trading Name" to the inputfield "#edit-trading-name"
#         And I click on the button "#edit-save"
#         Then the element "#edit-trading-names" contains the text "Different Trading Name"

#         # EDIT TRADING NAME

#         When I click the link text "edit trading name"
#         Then the element "h1.heading-xlarge" contains the text "Edit trading name for your organisation"
#         When I add "Change To Different Trading Name" to the inputfield "#edit-trading-name"
#         And I click on the button "#edit-save"
#         Then the element "#edit-trading-names" contains the text "Change To Different Trading Name"

        # ADD ORGANISATION CONTACT

        When I click the link text "add another organisation contact"
        And I add "Mr" to the inputfield "#edit-salutation"
        And I add "Added" to the inputfield "#edit-first-name"
        And I add "Contact" to the inputfield "#edit-last-name"
        And I add "02084445558" to the inputfield "#edit-work-phone"
        And I add "07865223222" to the inputfield "#edit-mobile-phone"
        And I add "added.contact@example.com" to the inputfield "#edit-email"
        And I add "Some additional notes for newly added contact" to the inputfield "#edit-notes"
        And I click on the button "#edit-save"
        And I click new person if suggestions displayed
        Then the element "#edit-organisation-contacts" contains the text "Added Contact"
        And the element "#edit-organisation-contacts" contains the text "02084445558"
        And the element "#edit-organisation-contacts" contains the text "07865223222"

        # EDIT MAIN ORGANISATION CONTACT

        When I click the link text "edit Added Contact"
        And I add "Ms" to the inputfield "#edit-salutation"
        And I add "Laura" to the inputfield "#edit-first-name"
        And I add "Lansing" to the inputfield "#edit-last-name"
        And I add "01234865432" to the inputfield "#edit-work-phone"
        And I add "07877943768" to the inputfield "#edit-mobile-phone"
        And I add "colin.weatherby@example.com" to the inputfield "#edit-email"
        And I click on the checkbox "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-save"
        Then the element "#edit-organisation-contacts" contains the text "Ms Laura Lansing"
        And the element "#edit-organisation-contacts" contains the text "01234865432"
        And the element "#edit-organisation-contacts" contains the text "07877943768 (preferred)"

        # COMPLETE CHANGES

        When I click on the button "#edit-save"
        And I select the option with the value "confirmed_business" for element "#edit-partnership-status-1"
        And I click on the button "#edit-submit-par-user-partnerships"
        And the element "#block-par-theme-content" contains the text "Organisation For Direct Partnership"
