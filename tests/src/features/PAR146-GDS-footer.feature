@ci @PAR-234
Feature: As a PAR user,
    I need to see the standard GDS Footer across the site
    so that I know I'm on the Primary Authority Register

    Scenario Outline: Valid Login  Id
        Given I open the url "<url>"
        Then I expect that element "#block-footer" is visible
        When I click on the link "Contact"
        Then I expect that element "h1" contains the text "Website feedback"
#        When I open the url "<url>"
#        And I click on the link "Open Government Licence"
#        Then I expect that the url is "https://www.nationalarchives.gov.uk/doc/open-government-licence/version/3/"
#        When I open the url "<url>"
#        And I click on the link "© Crown copyright"
#        Then I expect that the url is "http://www.nationalarchives.gov.uk/information-management/re-using-public-sector-information/uk-government-licensing-framework/crown-copyright/"

        Examples:
            | url |
            | /   |
