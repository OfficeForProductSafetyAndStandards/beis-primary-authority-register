Feature: User management

    @user-management @v2-ci-pending @smoke
    Scenario Outline: Verify users have permission to manage people
        Given I am logged in as "<user>"
        Then the element "#content" contains the text "Manage your colleagues"

        Examples:
            | user                       |
            | par_business@example.com	|
            | par_authority@example.com	|

    @user-management @v2-ci-pending
    Scenario: Verify an enforcement officer cannot added new person
        Given I am logged in as "par_enforcement_officer@example.com"
        Then the element "#content" does not contain the text "Manage your colleagues"

    @user-management @v2-ci-pending @smoke
    Scenario Outline: Add new person and invite user
        Given I am logged in as "par_authority_user_management@example.com"
        When I click the link text "Manage your colleagues"

        Then the element "h1.heading-xlarge" contains the text "People"
        When I click the link text "Add a person"

        # Enter the contact details
        Then the element "h1.heading-xlarge" contains the text "Add contact details"
        When I add "<title>" to the inputfield "#edit-salutation"
        And I add "<first_name>" to the inputfield "#edit-first-name"
        And I add "<last_name>" to the inputfield "#edit-last-name"
        And I add "<work_phone>" to the inputfield "#edit-work-phone"
        And I add "<mobile_phone>" to the inputfield "#edit-mobile-phone"
        And I add "<email>" to the inputfield "#edit-email"
        And I click on the button "#edit-next"

        # Choose to create an account
        Then the element "h1.heading-xlarge" contains the text "Give this person a user account?"
        When I click on the radio "#edit-account-new"
        And I click on the button "#edit-next"

        # Choose authorities for the person
        Then the element "h1.heading-xlarge" contains the text "Choose their memberships"
        When I click on the checkbox "#edit-par-component-memberships-select input"
        And I click on the button "#edit-next"

        # Choose role for the person
        Then the element "h1.heading-xlarge" contains the text "What type of user would you like to create?"
        When I click on the radio "#edit-role-par-<role>"
        And I click on the button "#edit-next"

        # Send invitation
        Then the element "h1.heading-xlarge" contains the text "Invite the person to create an account"
        And the element "#edit-recipient" contains the text "<email>"
        And I click on the button "#edit-next"

        # Check review page
        Then the element "h1.heading-xlarge" contains the text "Profile review"
        And the element "#edit-name" contains the text "<title> <first_name> <last_name>"
        And the element "#edit-email" contains the text "<email>"
        And the element "#edit-work-phone" contains the text "<work_phone>"
        And the element "#edit-mobile-phone" contains the text "<mobile_phone>"
        And the element "#edit-intro" contains the text "An invitation will be sent to this person to invite them to join the Primary Authority Register."
        And I click on the button "#edit-save"

        # Choose authorities for the person
        Then the element "h1.heading-xlarge" contains the text "Your new person has been created"
        And I click the link text "Done"
        Then the element "h1.heading-xlarge" contains the text "<title> <first_name> <last_name>"

        Examples:
            | email                                             | title | first_name    | last_name | work_phone    | mobile_phone  | role          |
            | par_user_management_second_officer@example.com    | Mr    | Joe           | Smith     | 01899 190 708 | 07111 000 111 | enforcement   |
            | par_user_management_second_contact@example.com    | Ms    | Hermione      | Ruth      | 01000 200 300 | 07999 999 999 | authority     |

    @user-management @v2-ci-pending
    Scenario Outline: Add new person without inviting the user
        Given I am logged in as "par_authority_user_management@example.com"
        When I click the link text "Manage your colleagues"

        Then the element "h1.heading-xlarge" contains the text "People"
        When I click the link text "Add a person"

        # Enter the contact details
        Then the element "h1.heading-xlarge" contains the text "Add contact details"
        When I add "<title>" to the inputfield "#edit-salutation"
        And I add "<first_name>" to the inputfield "#edit-first-name"
        And I add "<last_name>" to the inputfield "#edit-last-name"
        And I add "<work_phone>" to the inputfield "#edit-work-phone"
        And I add "<mobile_phone>" to the inputfield "#edit-mobile-phone"
        And I add "<email>" to the inputfield "#edit-email"
        And I click on the button "#edit-next"

        # Choose to create an account
        Then the element "h1.heading-xlarge" contains the text "Give this person a user account?"
        When I click on the radio "#edit-account-none"
        And I click on the button "#edit-next"

        # Choose authorities for the person
        Then the element "h1.heading-xlarge" contains the text "Choose their memberships"
        When I click on the checkbox "#edit-par-component-memberships-select input"
        And I click on the button "#edit-next"

        # Check review page
        Then the element "h1.heading-xlarge" contains the text "Profile review"
        And the element "#edit-name" contains the text "<title> <first_name> <last_name>"
        And the element "#edit-email" contains the text "<email>"
        And the element "#edit-work-phone" contains the text "<work_phone>"
        And the element "#edit-mobile-phone" contains the text "<mobile_phone>"
        And the element "#edit-intro" contains the text "A user account will not be created for this person."
        And I click on the button "#edit-save"

        # Choose authorities for the person
        Then the element "h1.heading-xlarge" contains the text "Your new person has been created"
        And I click the link text "Done"

        Examples:
            | email                                             | title | first_name    | last_name | work_phone    | mobile_phone  |
            | par_user_management_no_user@example.com           | Mrs   | Alex          | Welsh     | 01212 473 339 | 07824 994 544 |


    @user-management @v2-ci-pending
    Scenario: Check correct users can be managed
        Given I am logged in as "par_authority_user_management@example.com"

        # Check the dashboard links.
        Then the element "h1.heading-xlarge" contains the text "Primary Authority Register"
        And the element "#content" contains the text "People"
        When I click the link text "Manage your colleagues"

        # Confirm table and headings are correct.
        Then the element "h1.heading-xlarge" contains the text "People"
        And the element ".user-management-list .table-scroll-wrapper" is visible
        Then the element ".user-management-list .table-scroll-wrapper thead .views-field-last-name" contains the text "Name"
        And the element ".user-management-list .table-scroll-wrapper thead .views-field-par-person-email" contains the text "E-mail"
        And the element ".user-management-list .table-scroll-wrapper thead .views-field-par-flow-link" contains the text "Actions"

        # Check the correct users can be managed.
        And the element ".user-management-list .table-scroll-wrapper tbody" contains the text "par_authority_user_management@example.com"
        And the element ".user-management-list .table-scroll-wrapper tbody" contains the text "par_user_management_officer@example.com"
        And the element ".user-management-list .table-scroll-wrapper tbody" contains the text "par_user_management_contact@example.com"
        And the element ".user-management-list .table-scroll-wrapper tbody" does not contain the text "par_organisation_user_management@example.com"


    @user-management @v2-ci-pending
    Scenario: Check that users can be updated
        Given I am logged in as "par_authority_user_management@example.com"

        When I click the link text "Manage your colleagues"

        Then the element "h1.heading-xlarge" contains the text "People"
        When I add "par_user_management_multiple@example.com" to the inputfield "#edit-name-email-search"
        And I click on the button "#edit-submit-par-people"
        And I click the link text "Manage contact"

        # Check the profile view page.
        Then the element "h1.heading-xlarge" contains the text "Sally"
        And the element ".component-user-detail .heading-large" contains the text "User account"
        And the element ".component-user-detail" contains the text "par_user_management_multiple@example.com"
        And there is "2" occurences of element ".component-contact-locations-detail .component-item"

        # Update the user.
        When I click the link text "Update Dr Sally Michaels"

        Then the element "h1.heading-xlarge" contains the text "Update contact details"
        Then I add "Dr" to the inputfield "#edit-salutation"
        Then I add "Sally" to the inputfield "#edit-first-name"
        Then I add "McHaels" to the inputfield "#edit-last-name"
        And I add "01999 888 777" to the inputfield "#edit-work-phone"
        And I click on the button "#edit-next"

        Then the element "h1.heading-xlarge" contains the text "Update which authorities or organisations this person belongs to"
        When I click on the button "#edit-next"

        Then the element "h1.heading-xlarge" contains the text "Change the type of user"
        When I click on the button "#edit-next"

        Then the element "h1.heading-xlarge" contains the text "Profile review"
        And the element "#edit-personal" contains the text "Dr Sally McHaels"
        And the element "#edit-contact-details" contains the text "01999 888 777"
        When I click on the button "#edit-save"

        Then the element "h1.heading-xlarge" contains the text "Thank you for updating this person"
        And I click the link text "Done"

        # Check the user details have been updated and the contact records merged.
        Then the element "h1.heading-xlarge" contains the text "Profile"
        Then the element "h1.heading-xlarge" contains the text "Dr Sally McHaels"
        When I click the link text "Done"

        Then the element "h1.heading-xlarge" contains the text "People"
        When I add "par_user_management_multiple@example.com" to the inputfield "#edit-name-email-search"
        And I click on the button "#edit-submit-par-people"
        And I click the link text "Manage contact"

        And the element ".component-user-detail" contains the text "par_user_management_multiple@example.com"

    @user-management @v2-ci-pending
    Scenario: Check that user email addresses can be updated
        Given I am logged in as "par_authority_user_management@example.com"

        When I click the link text "Manage your colleagues"

        Then the element "h1.heading-xlarge" contains the text "People"
        When I add "par_user_management_officer@example.com" to the inputfield "#edit-name-email-search"
        And I click on the button "#edit-submit-par-people"
        And I click the link text "Manage contact"

        # Check the profile view page.
        Then the element "h1.heading-xlarge" contains the text "Emily Davidson"
        When I click the link text "Update Ms Emily Davidson"

        Then the element "h1.heading-xlarge" contains the text "Update contact details"
        Then I add "Mrs" to the inputfield "#edit-salutation"
        Then I add "Emilia" to the inputfield "#edit-first-name"
        Then I add "Daviddson" to the inputfield "#edit-last-name"
        Then I add "Daviddson" to the inputfield "#edit-last-name"
        And I add "01870446558" to the inputfield "#edit-work-phone"
        And I add "par_user_management_officer_2@example.com" to the inputfield "#edit-email"
        And I click on the button "#edit-next"

        Then the element "h1.heading-xlarge" contains the text "Give this person a user account?"
        Then the element "#edit-par-component-choose-account" contains the text "Keep the existing account: par_user_management_officer@example.com"
        Then the element "#edit-par-component-choose-account" contains the text "Invite par_user_management_officer_2@example.com to create a new account"
        When I click on the button "#edit-next"

        Then the element "h1.heading-xlarge" contains the text "Update which authorities or organisations this person belongs to"
        When I click on the button "#edit-next"

        Then the element "h1.heading-xlarge" contains the text "Change the type of user"
        When I click on the button "#edit-next"

        Then the element "h1.heading-xlarge" contains the text "Profile review"
        And the element "#edit-personal" contains the text "Mrs Emilia Daviddson"
        And the element "#edit-contact-details" contains the text "01870446558"
        When I click on the button "#edit-save"

        Then the element "h1.heading-xlarge" contains the text "Thank you for updating this person"
        And I click the link text "Done"


    @user-management @v2-ci-pending @smoke
    Scenario: Check that existing contacts can be invited
        Given I am logged in as "par_authority_user_management@example.com"

        When I click the link text "Manage your colleagues"

        Then the element "h1.heading-xlarge" contains the text "People"
        When I add "par_user_management_contact@example.com" to the inputfield "#edit-name-email-search"
        And I click on the button "#edit-submit-par-people"
        And I click the link text "Manage contact"

        # Update the user.
        When I click the link text "Invite the user to create an account"

        Then the element "h1.heading-xlarge" contains the text "What type of user would you like to create?"
        And I click on the radio "#edit-role-par-enforcement"
        When I click on the button "#edit-next"

        Then the element "h1.heading-xlarge" contains the text "Update which authorities or organisations this person belongs to"
        Then the element ".component-authority-select" contains the text "Choose an Authority"
        Then the element ".component-organisation-select" contains the text "There are no organisations to choose from."
        When I click on the button "#edit-next"

        Then the element "h1.heading-xlarge" contains the text "Invite the person to create an account"
        When I click on the button "#edit-next"

        Then the element "h1.heading-xlarge" contains the text "Invitation review"
        Then the element ".par-invite-review" contains the text "An invitation will be sent to this person to invite them to join the Primary Authority Register."
        When I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "The invitation has been sent"


    @user-management @v2-ci-pending @smoke
    Scenario: User should not be blocked
        Given I am logged in as "par_helpdesk@example.com"

        When I click the link text "Manage people"

        Then the element "h1.heading-xlarge" contains the text "People"
        When I add "par_authority_profile@example.com" to the inputfield "#edit-name-email-search"
        And I click on the button "#edit-submit-par-people"
        And I click the link text "Manage contact"

        # Confirm that this user cannot be cancelled.
        Then the element ".component-user-detail" contains the text "This user can not be removed because they are the only member of one of their authorities or organisations."

    @user-management @v2-ci-pending @smoke
    Scenario: User can be blocked and reactivated
        Given I am logged in as "par_helpdesk@example.com"

        When I click the link text "Manage people"

        Then the element "h1.heading-xlarge" contains the text "People"
        When I add "par_user_management_officer_2@example.com" to the inputfield "#edit-name-email-search"
        And I click on the button "#edit-submit-par-people"
        And I click the link text "Manage contact"

        # Confirm that this user can be cancelled.
        And I click the link text "Block user account"
        Then the element "h1.heading-xlarge" contains the text "Disable this user account"
        When I click on the button "#edit-next"
        Then the element ".component-user-detail" contains the text "The account is no longer active"

        # Confirm user can't sign in.
        And I click the link text "Sign out"
        Given I open the path "/user/login"
        And I add "par_user_management_officer_2@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then the element ".govuk-error-summary" contains the text "has not been activated or is blocked."

        Given I am logged in as "par_helpdesk@example.com"
        When I click the link text "Manage people"
        When I add "par_user_management_officer_2@example.com" to the inputfield "#edit-name-email-search"
        And I click on the button "#edit-submit-par-people"
        And I click the link text "Manage contact"

        # Confirm that this user can be reactivated.
        And I click the link text "Re-activate user account"
        Then the element "h1.heading-xlarge" contains the text "Re-activate this user account"
        When I click on the button "#edit-next"
        Then the element ".component-user-detail" contains the text "Last sign in"

        # Check user can sign back in.
        When I click the link text "Sign out"
        Given I am logged in as "par_user_management_officer_2@example.com"


