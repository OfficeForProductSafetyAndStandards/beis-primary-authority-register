@Pending
Feature: Edit Main Primary Authority Contact: Load form data - As a Primary Authority Officer
    I need to be able to edit the field 'Main Primary Authority Contact' in the existing partnership details;
    So that the correct details are taken forward into the new PAR

    Background:
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1/details/edit-primary-contact/1"

    Scenario: Edit Main Primary Authority Contact: Load form data
        Given I expect that element "h1" contains the text "You need to review and confirm the following partnerships"
