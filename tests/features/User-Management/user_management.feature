Feature: User Management
    As an Organisation user
    In order to on-board other uers on the service
    I should be able to add as new person


    @user-management
    Scenario Outline: Verify user in an organisation has permission to add new user
        Given I login as "<user>"
        Then I should see "Manage your colleagues" link

        Examples:
            | User                        |
            | par_coordinator@example.com |
            | par_admin@example.com       |
            | par_authority@example.com   |


    @user-management
    Scenario: Verify an enforcement officer cannot added new person
        Given I login as enforcement user
        Then I should not see "Manage your colleagues" link


    @user-management @happy path

    Scenario: Add new person
        Given I login as "par_authority@example.com"
        When I click on "Manage your colleagues" link
        And I fill in add new person form
        And I click on create account
        Then I should see confirmation message "Invitation sent out to new user"


    @user-management
    Scenario: Add duplicate user -existing email
        Given I login as "par_authority@example.com"
        And I click on "Manage your colleagues" link
        And I click on add new person
        And I fill-in form with existing email "par_coordinator@example.com"
        Then I should see error message user already exits



    @user-management @happy path

    Scenario: Verify I should be able add new user with existing user first and last name
        Given I login as "par_authority@example.com"
        And I click on "Manage your colleagues" link
        And I click on add new person
        And I fill-in form with existing user first/last name
        And I fill-in different unique email
        Then I should be able to create successfully


    @user-management
    Scenario: Update existing user
        Given
        When
        Then

    @user-management
    Sceenario: Update telephone

    @user-management
    Scenario: user not able to edit with existing email





