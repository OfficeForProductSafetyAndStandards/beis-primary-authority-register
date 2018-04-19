@ci
Feature: New Direct Partnership For PA To Approve

Background: 
        Given I am logged in as "par_authority@example.com"
    
    @directpartneship
    Scenario: New Direct Partnership   

        # PARTNERSHIP APPLY

        When I complete valid direct partnership application details

        # ADD ORGANISATION DETAIL

        When I complete valid organisation details for direct partnership "Organisation For Direct Partnership"

        # REVIEW THE APPLICATION

        And I complete review of the valid direct partnership application

        # INVITATIONS

        Then the direct partnership creation email template is correct

    @coordinatedpartneship
    Scenario: New coordinated Partnership

        # PARTNERSHIP APPLY

        When I complete valid coordinated partnership application details

        # ADD ORGANISATION DETAIL

        When I complete valid organisation details for coordinated partnership "Organisation For Coordinated Partnership"
        
        # REVIEW THE APPLICATION
        
        And I complete review of the valid coordinated partnership application
        
        # INVITATION
        
        Then the coordinated partnership creation email template is correct
    

        @coordinatedpartneship
    Scenario: New coordinated Partnership

        # PARTNERSHIP APPLY

        When I complete valid coordinated partnership application details

        # ADD ORGANISATION DETAIL

        When I complete valid organisation details for coordinated partnership "Organisation For Coordinated Partnership With No Members"
        
        # REVIEW THE APPLICATION
        
        And I complete review of the valid coordinated partnership application
        
        # INVITATION
        
        Then the coordinated partnership creation email template is correct