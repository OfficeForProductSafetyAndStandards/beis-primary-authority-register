Feature: User management

    @usermanagement @ci
    Scenario: Check correct users can be managed

        Given I am logged in as "par_authority_user_management@example.com"

        # Check the dashboard links.
        Then the element "h1.heading-xlarge" contains the text "Primary Authority Register"
        And the element "#content" contains the text "People"
        When I click the link text "Manage your colleagues"

        # Confirm table and headings are correct.
        Then the element "h1.heading-xlarge" contains the text "People"
        And the element ".user-management-list .table-scroll-wrapper" is visible
        When there is "3" occurences of element ".user-management-list .list-item-person"
        Then the element ".user-management-list .table-scroll-wrapper thead .views-field-last-name" contains the text "Name"
        And the element ".user-management-list .table-scroll-wrapper thead .views-field-email" contains the text "E-mail"
        And the element ".user-management-list .table-scroll-wrapper thead .views-field-par-flow-link" contains the text "Actions"

        # Check the correct users can be managed.
        And the element ".user-management-list .table-scroll-wrapper tbody" contains the text "par_authority_user_management@example.com"
        And the element ".user-management-list .table-scroll-wrapper tbody" contains the text "par_user_management_officer@example.com"
        And the element ".user-management-list .table-scroll-wrapper tbody" contains the text "par_user_management_contact@example.com"
        And the element ".user-management-list .table-scroll-wrapper tbody" does not contain the text "par_organisation_user_management@example.com"

    @usermanagement @ci
    Scenario: Check that users can be updated

        Given I am logged in as "par_authority_user_management@example.com"

        When I click the link text "Manage your colleagues"

        Then the element "h1.heading-xlarge" contains the text "People"
        When I add "par_user_management_officer@example.com" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-people"
        And I click the link text "Manage contact"

        # Check the profile view page.
        Then the element "h1.heading-xlarge" contains the text "Ms Emily Davidson"
        And the element ".component-user-detail .heading-large" contains the text "User account"
        And the element ".component-user-detail" contains the text "par_user_management_officer@example.com"
        When there is "2" occurences of element ".component-contact-detail .component-item"
        Then the element ".component-contact-detail .contact-detail-locations-1" contains the text "Contact at the authority: Authority for user management test"
        And the element ".component-contact-detail .contact-detail-locations-1" does not contain the text "Contact at the authority: Alternate authority for user management test"
        And the element ".component-contact-detail .contact-detail-locations-2" contains the text "Contact at the authority: Alternate authority for user management test"

        # Update the user.
        When I click the link text "Update user profile"

        Then the element "h1.heading-xlarge" contains the text "Choose which contact to update"
        And I click on the button "#edit-next"
    


#    @usermanagement @ci
#    Scenario: Add a new user
#
#        Given I am logged in as "par_authority_user_management@example.com"
#
#    @usermanagement @ci
#    Scenario: Check that contacts can be invited
#
#        Given I am logged in as "par_authority_user_management@example.com"
