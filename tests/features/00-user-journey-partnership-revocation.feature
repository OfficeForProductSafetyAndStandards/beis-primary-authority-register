@ci
Feature: New Direct Partnership For HD user To Revoke

    Scenario: New Direct Partnership For HD user To Revoke
        
        #LOGIN
        Given I am logged in as "par_authority@example.com"

        # PARTNERSHIP APPLY
        When I complete valid direct partnership application details

        # ADD ORGANISATION DETAIL
        When I complete valid organisation details for direct partnership "Direct Partnership For Revoking8"

        # REVIEW THE APPLICATION
        And I complete review of the valid direct partnership application

        # INVITATION
        And I check the email confirmations have processed correctly

    