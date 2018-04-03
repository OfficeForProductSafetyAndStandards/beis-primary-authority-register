    @ci
    Feature: Helpdesk Enforcement Notice Management

    Scenario: Enforcement notice approval

        #LOGIN
        
        Given I am logged in as "par_helpdesk@example.com"
        When I click the link text "Dashboard"
        And I click the link text "See enforcement notifications received"
        And I select the option with the value "notice_date DESC" for element "#edit-sort-bef-combine"
        And I click on the button "#edit-submit-par-user-enforcement-list"

        # APPROVAL FORM

        And I click the link text "Title of enforcement notice 5"
        And I click on the radio "#edit-actions-0-primary-authority-status-approved"
        And I click on the button "#edit-actions-next"
        Then the element ".heading-secondary" contains the text "Confirmation"
        Then the element "h1.heading-xlarge" contains the text "Enforcement action decision"
        And I click on the button "#edit-actions-next"
        # Then the element "#par-rd-help-desk-approve" contains the text "Enforcement allowed"
        And I click the link text "Log out"

        # CHECK PAR ENFORCEMENT OFFICER VIEW

        Given I am logged in as "par_enforcement_officer@example.com"
        When I click the link text "See enforcement notifications sent"
        And the element ".table-scroll-wrapper" does not contain the text "Title of enforcement notice 5"