Feature: New Direct Partnership For PA To Approve

    Background: 
        Given I am logged in as "par_authority@example.com"
    
    @directpartneship @setup
    Scenario: New Direct Partnership   

        # PARTNERSHIP APPLY

        When I complete valid direct partnership application details

        # ADD ORGANISATION DETAIL

        When I complete valid organisation details for direct partnership "Organisation For Updating Direct Partnership"

        # REVIEW THE APPLICATION

        And I complete review and submit valid direct partnership application

        # INVITATIONS

        Then the direct partnership creation email template is correct

    @coordinatedpartnership @setup
    Scenario: New coordinated Partnership

        # PARTNERSHIP APPLY

        When I complete valid coordinated partnership application details

        # ADD ORGANISATION DETAIL

        When I complete valid organisation details for coordinated partnership "Organisation For Updating Coordinated Partnership"
        
        # REVIEW THE APPLICATION
        
        And I complete review and submit valid direct partnership application
        
        # INVITATION
        
        Then the coordinated partnership creation email template is correct

            @coordinatedpartnership @setup
    Scenario: New coordinated Partnership

        # PARTNERSHIP APPLY

        When I complete valid coordinated partnership application details

        # ADD ORGANISATION DETAIL

        When I complete valid organisation details for coordinated partnership "Organisation For Uploading To Coordinated Partnership"
        
        # REVIEW THE APPLICATION
        
        And I complete review and submit valid direct partnership application
        
        # INVITATION
        
        Then the coordinated partnership creation email template is correct
    
        @coordinatedpartnership  @setup
    Scenario: New coordinated Partnership

        # PARTNERSHIP APPLY

        When I complete valid coordinated partnership application details

        # ADD ORGANISATION DETAIL

        When I complete valid organisation details for coordinated partnership "Organisation For No Members Coordinated Partnership"
        
        # REVIEW THE APPLICATION
        
        And I complete review and submit valid coordinated partnership application
        
        # INVITATION
        
        Then the coordinated partnership creation email template is correct