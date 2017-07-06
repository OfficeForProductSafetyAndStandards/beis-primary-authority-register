@Pending
Feature: Create Partnerships

    Background:
        Given I open the url "/login"
        And I add "PrimaryAuthorityOfficer" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Logged in"

    Scenario: Create New Partnership
        Given I open the url "/manage-partnerships"
        And I click on the radio "#create-new-partnership"
        And I press "Continue"
        Then I expect that element "#header" contains the text "New Partnership Application"
        When I click on the link "#adequate-resource"
        And I click on the link "#competence-in-regulatory-areas"
        And I click on the link "authority-to-give-advice"
        And I click on the link "#suitable-cost-recovery-measures"
        And I click on the link "#confirm-authorised"
        When I press "Continue"
        Then I expect that element "#header" contains the text "Partnership contact details"
        When I click on the link "#main-primary-authority-contact"
        And I press "Continue"
        Then I expect that element "#primary-authority-contact-heading" is visible
        Then I expect that element "#primary-authority-contact-email" is visible
        When I add "07729678433" to the inputfield "#primary-authority-contact-mobile"
        And I click on the radio "#primary-authority-contact-preferred-email"
        And I add "some notes" to the inputfield "#primary-authority-contact-notes"
        And I click on the radio "#primary-authority-contact-add-another-no"
        When I press "Continue"
        Then I expect that element "#business-name-heading" is visible
        When I add "Test Company 1" to the inputfield "#business-name"
        And I press "Continue"
        Then I expect that element "#existing-partnerships-check-heading" is visible
        When I click on the radio "#new-partnership"
        And I press "Continue"
        Then I expect that element "#business-address-details-heading" is visible
        When I add "E1 3BG" to the inputfield "#business-postcode"
        And I press "Lookup"
        And I select the option with the value "1 The Street, LittleTown, Big City" for element "#address-1"
        When I press "Select address"
        Then I expect that element "#buisness-address-details" is visible
        And I press "Continue"
        Then I expect that element "#business-main-contact-details" is visible
        When I add "Mr" to the inputfield "#business-main-contact-title"
        And I add "John" to the inputfield "#business-main-contact-first-name"
        And I add "0207 456 7893" to the inputfield "#business-main-contact-work-phone"
        And I add "07734 567 679" to the inputfield "#business-main-contact-mobile-number"
        And I add "xyz@abc.com" to the inputfield "#business-main-contact-email"
        And I add "some contact notes" to the inputfield "#business-main-contact-notes"
        And I click on the radio "#business-main-contact-add-no"
        When I press "Continue"
        Then I expect that element "#environmental-health-contact" is visible
        When I click on the radio "#environmental-health-contact-1"
        And I press "Continue"
        Then I expect that element "#trading-standards-contact" is visible
        When I click on the radio "#trading-standards-contact-1"
        And I press "Continue"
        Then I expect that element "#licencing-contact" is visible
        When I click on the radio "#licencing-contact-1"
        And I press "Continue"
        Then I expect that element "#partnership-information" is visible
        When I add "some extra information about partnership" to the inputfield "#partnership-information"
        And I press "Continue"
        Then I expect that element "#partnership-information-review" is visible
        When I click on the checkbox "#partnership-information-confirm"
        And I press "Continue"
        Then I expect that element "#partnership-complete-notification" is visible
        When I click on the radio "#send-to-business-for-completion"
        And I press "Continue"
        Then I expect that element "#business-contact-mail-sent-success" is visible
