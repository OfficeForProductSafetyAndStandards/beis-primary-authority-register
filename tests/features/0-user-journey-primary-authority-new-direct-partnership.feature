@ci
Feature: New Direct Partnership For PA To Approve

    Scenario: New Direct Partnership
        
        #LOGIN
        Given I am logged in as "par_authority@example.com"

        # PARTNERSHIP APPLY
        When I complete valid direct partnership application details

        # ADD ORGANISATION DETAIL
        When I complete valid organisation details for direct partnership "Organisation For Direct Partnership 4"

        # REVIEW THE APPLICATION
        And I complete review of the valid direct partnership application

        # INVITATION
        And I check the email confirmations have processed correctly
