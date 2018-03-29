@ci @PAR790
Feature: Coordinator User - Upload Members

    Scenario: Coordinator User - Upload Members

        # PARTNERSHIPS DASHBOARD
        Given I open the url "/user/login"
        And I add "par_coordinator@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element "#block-par-theme-content" contains the text "See your partnerships"
        And I click on the link "See your partnerships"
        And I click on the link "Organisation For Coordinated Partnership 20"
        Then I expect that element "h1" is not empty

        # UPLOAD MEMBERS

        When I click on the link "Show members list"
        Then I expect that element "h1.heading-xlarge" contains the text "Member list"
        # And I run tota11y against the current page
        When I click on the link "Upload a Member List (CSV)"
        Then I expect that element "h1.heading-xlarge" contains the text "Upload a list of members"

        # SUPPORT LINKS

        And I expect that element "a=Member Guidance Page" does exist
        And I expect that element "a=Download link" does exist

        # VALIDATION

        When I upload the file "./files/csv_test_errors.csv" to field "#edit-csv-upload"
        And I click on the button "#edit-upload"
        Then I expect that element "h1.heading-xlarge" contains the text "CSV validation errors"
        And I expect that element "a=Member Guidance Page" does exist
        Then I expect that element "#edit-error-list tbody" contains the text "Line"
        And I expect that element "#edit-error-list thead" contains the text "Column"
        And I expect that element "#edit-error-list thead" contains the text "Error"
        And I expect that element "#edit-error-list thead" contains the text "Organisation name"
        And I expect that element "#edit-error-list thead" contains the text "Membership Start Date"
        And I expect that element "#edit-error-list thead" contains the text "Address Line 1"
        And I expect that element "#edit-error-list thead" contains the text "Nation"
        When I click on the button "#edit-done"

        # CSV PROCESSING

        Then I expect that element "h1.heading-xlarge" contains the text "Upload a list of members"
        When I upload the file "./files/csv_test_valid.csv" to field "#edit-csv-upload"
        And I click on the button "#edit-upload"
        Then I expect that element "h1.heading-xlarge" contains the text "Confirm member upload"
        And I click on the button "#edit-save"
