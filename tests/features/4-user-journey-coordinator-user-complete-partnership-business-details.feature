Feature: Coordinator User - Complete organisation details

    @coordinatedpartnership @ci
    Scenario: Coordinator User - Complete organisation details

        #LOGIN
        
        Given I am logged in as "par_coordinator@example.com"
        And I go to partnership detail page for my partnership "Organisation For Coordinated Partnership" with status "confirmed_authority"

        # COMPLETE ABOUT THE BUSINESS

        And I complete about the business

        # COMPLETE REGISTERED ADDRESS

        And I complete the organisation registered address for coordinated partnership

        # COMPLETE BUSINESS CONTACT DETAILS

        And I complete the business contact details

        # COMPLETE SIC CODES
        
        And I complete the SIC codes

        # COMPLETE ORGANISATION SIZE

        Then the element "h1.heading-xlarge" contains the text "Confirm the size of the membership list"
        And I select the option with the value "small" for element "#edit-business-size"
        And I click on the button "#edit-next"
        
        # COMPLETE TRADING NAME

        And I complete the trading names

        # COMPLETE LEGAL ENTITY

        And I complete the legal entities

        # REVIEW

        And I submit final confirmation of completion by organisation "Organisation For Coordinated Partnership"

        # Then the element "h1.heading-xlarge" contains the text "Check partnership information"
        # Then the element "#edit-organisation-registered-address" contains the text "1 High St"
        # And the element "#edit-organisation-registered-address" contains the text "Southwark"
        # And the element "#edit-organisation-registered-address" contains the text "London"
        # And the element "#edit-organisation-registered-address" contains the text "SE16 4NX"
        # Then the element "#edit-about-organisation" contains the text "Some information about organisation details"
        # Then the element "#edit-sic-code" contains the text "Social care activities without accommodation"
        # Then the element "#edit-legal-entities" contains the text "New LLP Company"
        # Then the element "#edit-legal-entities" contains the text "Limited Liability Partnership"
        # Then the element "#edit-legal-entities" contains the text "1234567890"
        # And I click on the checkbox "#edit-terms-organisation-agreed"
        # And I click on the button "#edit-save"
        # Then the element "h1.heading-xlarge" contains the text "Thank you for completing the application"
        # And I click on the button ".button"


    @coordinatedpartnership
    Scenario: Coordinator User - Complete organisation details 2

        #LOGIN
        
        Given I am logged in as "par_coordinator@example.com"
        And I go to partnership detail page for my partnership "Organisation For No Members Coordinated Partnership" with status "confirmed_authority"

        # COMPLETE ABOUT THE BUSINESS

        And I complete about the business

        # COMPLETE REGISTERED ADDRESS

        And I complete the organisation registered address for coordinated partnership

        # COMPLETE BUSINESS CONTACT DETAILS

        And I complete the business contact details

        # COMPLETE SIC CODES
        
        And I complete the SIC codes

        # COMPLETE ORGANISATION SIZE

        Then the element "h1.heading-xlarge" contains the text "Confirm the size of the membership list"
        And I select the option with the value "small" for element "#edit-business-size"
        And I click on the button "#edit-next"
        
        # COMPLETE TRADING NAME

        And I complete the trading names

        # COMPLETE LEGAL ENTITY

        And I complete the legal entities

        # REVIEW

        And I submit final confirmation of completion by organisation "Organisation For No Members Coordinated Partnership"

        # Then the element "h1.heading-xlarge" contains the text "Check partnership information"
        # Then the element "#edit-organisation-registered-address" contains the text "1 High St"
        # And the element "#edit-organisation-registered-address" contains the text "Southwark"
        # And the element "#edit-organisation-registered-address" contains the text "London"
        # And the element "#edit-organisation-registered-address" contains the text "SE16 4NX"
        # Then the element "#edit-about-organisation" contains the text "Some information about organisation details"
        # Then the element "#edit-sic-code" contains the text "Social care activities without accommodation"
        # Then the element "#edit-legal-entities" contains the text "New LLP Company"
        # Then the element "#edit-legal-entities" contains the text "Limited Liability Partnership"
        # Then the element "#edit-legal-entities" contains the text "1234567890"
        # And I click on the checkbox "#edit-terms-organisation-agreed"
        # And I click on the button "#edit-save"
        # Then the element "h1.heading-xlarge" contains the text "Thank you for completing the application"
        # And I click on the button ".button"
