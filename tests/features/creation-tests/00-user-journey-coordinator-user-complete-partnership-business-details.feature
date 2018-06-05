Feature: Coordinator User - Complete organisation details

   @coordinatedpartnership @setup
    Scenario: Coordinator User - Complete organisation details

        #LOGIN
        
        Given I am logged in as "par_coordinator@example.com"
        And I go to partnership detail page for my partnership "Organisation For Updating Coordinated Partnership" with status "confirmed_authority"

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

        And I submit final confirmation of completion by organisation "Organisation For Updating Coordinated Partnership"


    @coordinatedpartnership
    Scenario: Coordinator User - Complete organisation details

        #LOGIN
        
        Given I am logged in as "par_coordinator@example.com"
        And I go to partnership detail page for my partnership "Organisation For Uploading To Coordinated Partnership" with status "confirmed_authority"

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

        And I submit final confirmation of completion by organisation "Organisation Uploading To Coordinated Partnership"


    @coordinatedpartnership @setup
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

        # Then the element "h1.heading-xlarge" contains the text "Review the partnership summary information below"


