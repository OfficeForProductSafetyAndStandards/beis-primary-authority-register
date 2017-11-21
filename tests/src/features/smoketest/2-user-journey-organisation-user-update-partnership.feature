@smoketest
Feature: Business User - Manage Addresses

    Scenario: Business User - Manage Addresses

        # PARTNERSHIPS DASHBOARD

        Given I am logged in as "par_business@example.com"
        When I open the url "/dashboard"
        And I click on the link "See your partnerships"
        And I click on the link "Business For Direct Partnership 1"
        And I expect that element "h1" is not empty

        # EDIT REGISTERED ADDRESS

        When  I click on the link "edit address"
        And I clear the inputfield "#edit-address-line1"
        And I add "1 Change St" to the inputfield "#edit-address-line1"
        When I click on the button "#edit-save"
        Then I expect that element "#edit-registered-address" contains the text "1 Change St"

        # EDIT ABOUT THE BUSINESS

        When I click on the link "edit about the organisation"
        And I add "Change to the about business details section" to the inputfield "#edit-about-business"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-about-business" contains the text "Change to the about business details section"

        # ADD SIC CODES

        When I click on the link "add another sic code"
        And I select the option with the text "10-0 Health and social care" for element "#edit-sic-code"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-sic-codes" contains the text "Health and social care"

        # ADD EMPLOYEES

        When I click on the link "edit number of employees"
        And I select the option with the text "50-249" for element "#edit-employees-band"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-employee-no" contains the text "50-249"

        # EDIT LEGAL ENTITY

#        When I click on the link "edit legal entity"
#        And I clear the inputfield "#edit-registered-name"
#        When I add "Changed" to the inputfield "#edit-registered-name"
#        And I select the option with the text "Limited Company" for element "#edit-legal-entity-type"
#        And I clear the inputfield "#edit-company-house-no"
#        When I add "0123456789" to the inputfield "#edit-company-house-no"
#        And I click on the button "#edit-save"
#        Then I expect that element "#edit-legal-entities" contains the text "Changed"
#        Then I expect that element "#edit-legal-entities" contains the text "Limited Company"

        # ADD LEGAL ENTITY

#        When I click on the link "add another legal entity"
#        When I add "New Legal Entity" to the inputfield "#edit-registered-name"
#        And I select the option with the text "Partnership" for element "#edit-legal-entity-type"
#        And I click on the button "#edit-save"
#        Then I expect that element "#edit-legal-entities" contains the text "New Legal Entity"
#        Then I expect that element "#edit-legal-entities" contains the text "Partnership"

        # ADD NEW TRADING NAME

        When I click on the link "add another trading name"
        When I add "Different Trading Name" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-trading-names" contains the text "Different Trading Name"

        # EDIT MAIN BUSINESS CONTACT

# EDIT MAIN BUSINESS CONTACT

        When I click on the link "edit organisation contact"
        And I add "Harvey" to the inputfield "#edit-first-name"
        And I add "Kneeslapper" to the inputfield "#edit-last-name"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-organisation-contacts" contains the text "Harvey"
        And I expect that element "#edit-organisation-contacts" contains the text "Kneeslapper"

        # COMPLETE CHANGES

        And I click on the button "#edit-save"
