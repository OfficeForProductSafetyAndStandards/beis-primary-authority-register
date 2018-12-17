Feature: User management

    @usermanagement @ci
    Scenario: Check correct users can be managed

        Given I am logged in as "par_authority_user_management@example.com"

        # Check the dashboard links.
        Then the element "h1.heading-xlarge" contains the text "Primary Authority Register"
        And the element "#content" contains the text "People"
        And I click the link text "Manage your colleagues"

        # Confirm table and headings are correct.
        And the element ".user-management-list .table-scroll-wrapper" is visible
        And the element "user-management-list .table-scroll-wrapper thead .views-field-last-name" contains the text "Name"
        And the element "user-management-list .table-scroll-wrapper thead .views-field-email" contains the text "E-mail"
        And the element "user-management-list .table-scroll-wrapper thead .views-field-par-flow-link" contains the text "Actions"

        # Check the correct users can be managed.
        And the element "user-management-list .table-scroll-wrapper tbody" contains the text "par_authority_user_management@example.com"
        And the element "user-management-list .table-scroll-wrapper tbody" contains the text "par_user_management_officer@example.com"
        And the element "user-management-list .table-scroll-wrapper tbody" contains the text "par_user_management_contact@example.com"
        And the element "user-management-list .table-scroll-wrapper tbody" does not contain the text "par_organisation_user_management@example.com"

#    @usermanagement @ci
#    Scenario: Check that users can be updated
#
#        Given I am logged in as "par_authority_user_management@example.com"
#
#    @usermanagement @ci
#    Scenario: Add a new user
#
#        Given I am logged in as "par_authority_user_management@example.com"
#
#    @usermanagement @ci
#    Scenario: Check that contacts can be invited
#
#        Given I am logged in as "par_authority_user_management@example.com"
