    Feature: Enforcement Officer - Issue enforcement notice
    
    @ci
    Scenario Outline: Enforcement Officer - Issue enforcement notice on coordinated partnership with no members

        #LOGIN
        
        Given I am logged in as "par_enforcement_officer@example.com"
        And I go to partnership detail page for my partnership "Organisation For Coordinated Partnership With No Members"

       # ENFORCEMENT ACTION FORM

       When I click the link text "Send a notification of a proposed enforcement action"

       # CHOOSE MEMBER

        And I click on authority selection if available
#        When I store all EO data to use in later step

       When I click on the button "#edit-save"
       Then the element "h1.heading-xlarge" contains the text "Primary Authority Register"


        #       CHECK RECEIVED ENFORCEMENT NOTIFICATIONS

        When I open the path "/user/logout"
        And I open the path "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        When I click the link text "See enforcement notifications received"
        Then the element ".table-scroll-wrapper" contains the text "Enforcement notice 4"

