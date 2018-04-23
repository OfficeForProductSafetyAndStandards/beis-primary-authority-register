Feature: Business User - Complete organisation details

    @ci
    Scenario: Business User - Complete organisation details

        # LOGIN
        
        Given I am logged in as "par_business@example.com"

        # COMPLETE PARTNERSHIP ORGANISATION DETAIL

        And I go to partnership detail page for my partnership "Organisation For Direct Partnership"

        # EDIT ABOUT THE BUSINESS

        And I complete about the business

        # EDIT REGISTERED ADDRESS

        And I complete the organisation registered address
        
        # ADD SIC CODES

        And I complete the SIC codes

        # ADD EMPLOYEES

        And I complete the employees
        
        # ADD TRADING NAME

        And I complete the trading names

        # ADD LEGAL ENTITY

        And I complete the legal entities

        # CHANGE ABOUT BUSINESS

        And I change the completed about the organisation

        #  CHANGE LEGAL ENTITIES

        And I change the completed legal entities

        # REVIEW PARTNERSHIP

        And I submit final confirmation of completion by organisation "Organisation For Direct Partnership"
