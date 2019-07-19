Feature: Business User - Complete organisation details

    @ci @directpartnership
    Scenario: Business User - Complete organisation details

        # LOGIN
        
        Given I am logged in as "par_business@example.com"

        # COMPLETE PARTNERSHIP ORGANISATION DETAIL

        And I go to partnership detail page for my partnership "Organisation For Direct Partnership" with status "confirmed_authority"

        # COMPLETE ABOUT THE BUSINESS

        And I complete about the business

        # COMPLETE REGISTERED ADDRESS

        And I complete the organisation registered address for direct partnership
        
        # COMPLETE SIC CODES

        And I complete the SIC codes

        # COMPLETE EMPLOYEES

        And I complete the employees
        
        # COMPLETE TRADING NAME

        And I complete the trading names

        # COMPLETE LEGAL ENTITY

        And I complete the legal entities

        # CHANGE ABOUT BUSINESS

        And I change the completed about the organisation

        #  CHANGE LEGAL ENTITIES

        And I change the completed legal entities

        # REVIEW PARTNERSHIP

        And I submit final confirmation of completion by organisation "Organisation For Direct Partnership"


    @ci @directpartnership
    Scenario: Business User - Cannot modify legal entities
        Given I am logged in as "par_business@example.com"
        And I go to partnership detail page for my partnership "Partnership nominated by Secretary of State" with status "confirmed_rd"
        Then the element "h1.heading-xlarge" contains the text "Partnership nominated by Secretary of State"
        And the element "#edit-legal-entities" does not contain the text "add another legal entity"
        And I go to partnership detail page for my partnership "Partnership confirmed by organisation" with status "confirmed_business"
        Then the element "h1.heading-xlarge" contains the text "Partnership confirmed by organisation"
        And the element "#edit-legal-entities" does not contain the text "add another legal entity"


    @ci @directpartnership
    Scenario: Helpdesk User - Cannot modify legal entities on active partnerships
        Given I am logged in as "par_helpdesk@example.com"
        And I go to manage the partnership "Partnership nominated by Secretary of State" with status "confirmed_rd"
        Then the element "h1.heading-xlarge" contains the text "Partnership nominated by Secretary of State"
        And the element "#edit-legal-entities" does not contain the text "add another legal entity"
        And I go to manage the partnership "Partnership confirmed by organisation" with status "confirmed_business"
        Then the element "h1.heading-xlarge" contains the text "Partnership confirmed by organisation"
        And the element "#edit-legal-entities" contains the text "add another legal entity"
