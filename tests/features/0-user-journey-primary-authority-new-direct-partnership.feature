@ci
Feature: New Direct Partnership For PA To Approve

Background: 
        Given I am logged in as "par_authority@example.com"
    
    Scenario: New Direct Partnership
        
      # PARTNERSHIP APPLY
        When I complete valid direct partnership application details

        # ADD ORGANISATION DETAIL
        When I complete valid organisation details for direct partnership "Organisation For Direct Partnership"

        # REVIEW THE APPLICATION
        And I complete review of the valid direct partnership application

        # INVITATION
        And I check the email confirmations have processed correctly
    
    Scenario: New Direct Partnership For HD user To Revoke
        
        # PARTNERSHIP APPLY
        When I complete valid direct partnership application details

        # ADD ORGANISATION DETAIL
        When I complete valid organisation details for direct partnership "Organisation For Direct Partnership To Revoke"

        # REVIEW THE APPLICATION
        And I complete review of the valid direct partnership application

        # INVITATION
        And I check the email confirmations have processed correctly