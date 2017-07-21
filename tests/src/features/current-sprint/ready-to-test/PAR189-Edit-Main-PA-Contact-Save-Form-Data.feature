@Pending
Feature:  Edit Main Primary Authority Contact: Save form data - As a Primary Authority Officer
I need to be able to edit the field 'Main Primary Authority Contact' in the existing partnership details;
So that the correct details are taken forward into the new PAR

    Background:
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1/details"

    Scenario: List Partnership Details: Load summary elements Load data into the form
        Then I expect that element "h1" contains the text "Viewing/confirming partnership details"
