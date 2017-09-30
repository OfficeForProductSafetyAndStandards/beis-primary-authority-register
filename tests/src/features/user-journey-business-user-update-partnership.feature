@Pending @journey2 @deprecated
Feature: Business User - Manage Addresses

  Background:
      # TEST DATA RESET
    Given I reset the test data

  Scenario: Business User - Manage Addresses
      # LOGIN SCREEN

    Given I am logged in as "par_business@example.com"
    And I open the url "/dashboard"
    When I click on the link "See your partnerships"

      # PARTNERSHIPS DASHBOARD FILTERS

    And I add "DCBA" to the inputfield "#edit-keywords"
    And I click on the button "#edit-submit-par-user-partnerships"
    Then I expect that element "td.views-field.views-field-nothing" is not visible
    When I add "Co Mart" to the inputfield "#edit-keywords"
    And I click on the button "#edit-submit-par-user-partnerships"
    Then I expect that element "views-field views-field-par-flow-link-1" contains the text "Co Mart"
    When I select the option with the text "Confirmed by Business" for element "#edit-partnership-status"
    And I click on the button "#edit-submit-par-user-partnerships"
    Then I expect that element "views-field views-field-par-flow-link-1" is not visible
    When I select the option with the text "Awaiting Review" for element "#edit-partnership-status"
    And I click on the button "#edit-submit-par-user-partnerships"
    Then I expect that element "views-field views-field-par-flow-link-1" contains the text "Co Mart"
    When I click on the button "a*=Co Mart"

      # TERMS AND CONDITIONS SCREEN

    Then I expect that element "#par-flow-transition-business-terms" contains the text "Please review the new Primary Authority terms and conditions and confirm that you agree with them"
    And I click on the button "#edit-save"
    Then I expect that element ".error-summary" contains the text "You must agree to the new terms and conditions"
    And I click on the checkbox "#edit-terms-conditions"
    And I click on the button "#edit-save"

      # PARTNERSHIP TASKS

    When I click on the link "Review and confirm your business details"
    Then I expect that element "#edit-about-business" contains the text "About the business"
    And I expect that element "#edit-legal-entity" contains the text "Legal Entities"
    And I expect that element "#edit-primary-contact" contains the text "Main business contact"
    And I expect that element "#edit-0" contains the text "Trading Names"

      # EDIT REGISTERED ADDRESS

    When  I click on the button "form#par-flow-transition-business-details #edit-primary-address a.flow-link"
    And I clear the inputfield "#edit-address-line1"
    And I clear the inputfield "#edit-address-line2"
    And I clear the inputfield "#edit-town-city"
    And I clear the inputfield "#edit-postcode"
    And I clear the inputfield "#edit-county"
    And I click on the button "#edit-save"
    Then I expect that element ".error-message" does exist
    When I add "SE16 4NX" to the inputfield "#edit-postcode"
    And I click on the button "#edit-save"
    Then I expect that element ".error-message" does exist
    And I add "1 Change St" to the inputfield "#edit-address-line1"
    And I click on the button "#edit-save"
    Then I expect that element ".error-message" does exist
    And I add "New Change" to the inputfield "#edit-address-line2"
    And I click on the button "#edit-save"
    Then I expect that element ".error-message" does exist
    When I add "London" to the inputfield "#edit-town-city"
    And I click on the button "#edit-save"
    Then I expect that element ".error-message" does exist
    When I add "London" to the inputfield "#edit-county"
    And I select the option with the text "England" for element "#edit-country"
    When I click on the button "#edit-save"
    Then I expect that element "span.address-line1" contains the text "1 Change St"
    And I expect that element "span.address-line2" contains the text "New Change"
    And I expect that element "span.locality" contains the text "London"
    And I expect that element "span.postal-code" contains the text "SE16 4NX"
    And I expect that element "#edit-primary-address" contains the text "England"

      # EDIT MAIN BUSINESS CONTACT

    When I click on the button "form#par-flow-transition-business-details #edit-primary-contact a.flow-link"
    And I add "Harvey" to the inputfield "#edit-first-name"
    And I add "Kneeslapper" to the inputfield "#edit-last-name"
    And I add "999999999" to the inputfield "#edit-work-phone"
    And I add "1111111111111" to the inputfield "#edit-mobile-phone"
    And I add "02079999999" to the inputfield "#edit-work-phone"
    And I add "078659999999" to the inputfield "#edit-mobile-phone"
    And I add "par_business_change@example.com" to the inputfield "#edit-email"
    And I click on the radio "#edit-preferred-contact-communication-mobile"
    And I add "Some additional notes" to the inputfield "#edit-notes"
    And I click on the button "#edit-save"
    Then I expect that element "#edit-primary-contact" contains the text "Harvey"
    And I expect that element "#edit-primary-contact" contains the text "Kneeslapper"
    And I expect that element "#edit-primary-contact" contains the text "par_business_change@example.com"
    And I expect that element "#edit-primary-contact" contains the text "2079999999"
    And I expect that element "#edit-primary-contact" contains the text "78659999999"

      # EDIT ALTERNATE CONTACT

    When I click on the button "form#par-flow-transition-business-details #edit-alternative-people a.flow-link"
    And I add "Herbert" to the inputfield "#edit-first-name"
    And I add "Birdsfoot" to the inputfield "#edit-last-name"
    And I add "01723999999" to the inputfield "#edit-work-phone"
    And I add "08654999999" to the inputfield "#edit-mobile-phone"
    And I add "par_business_change@example.com" to the inputfield "#edit-email"
    And I click on the radio "#edit-preferred-contact-communication-mobile"
    And I click on the button "#edit-save"
    Then I expect that element "#edit-alternative-people" contains the text "Herbert"
    And I expect that element "#edit-alternative-people" contains the text "Birdsfoot"
    And I expect that element "#edit-alternative-people" contains the text "par_business_change@example.com"
    And I expect that element "#edit-alternative-people" contains the text "01723999999"
    And I expect that element "#edit-alternative-people" contains the text "08654999999"

      # EDIT LEGAL ENTITIES

    When I click on the button "form#par-flow-transition-business-details #edit-legal-entity a.flow-link"
    And I add "Legal Entity Change" to the inputfield "#edit-registered-name"
    And I select the option with the text "Limited Company" for element "#edit-legal-entity-type"
    And I add "987654321" to the inputfield "#edit-company-house-no"
    And I click on the button "#edit-save"
    Then I expect that element "#edit-legal-entity" contains the text "Legal Entity Change"
    And I expect that element "#edit-legal-entity" contains the text "987654321"
    And I expect that element "#edit-legal-entity" contains the text "Limited Company"

      # ADD LEGAL ENTITIES

    When I click on the link "add another legal entity"
    And I click on the button "#edit-save"
    And I add "Another Legal Entity" to the inputfield "#edit-registered-name"
    And I select the option with the text "Sole Trader" for element "#edit-legal-entity-type"
    And I click on the button "#edit-save"

      # PARTNERSHIP TASKS

    Then I expect that element "#par-flow-transition-business-details" contains the text "Another Legal Entity"
    And I expect that element "#par-flow-transition-business-details" contains the text "Sole Trader"

      # REVIEW BUSINESS NAME AND SUMMARY

    And I click on the link "edit"
    And I add "Change to the about business details section" to the inputfield "#edit-about-business"
    And I click on the button "#edit-save"
    Then I expect that element "#edit-about-business" contains the text "Change to the about business details section"
    When I click on the button "form#par-flow-transition-business-details #edit-0.js-form-item a.flow-link"
    And I add "Trading Name Change" to the inputfield "#edit-trading-name"
    And I click on the button "#edit-save"
    Then I expect that element "#par-flow-transition-business-details" contains the text "Trading Name Change"
    When I click on the link "add another trading name"
    And I click on the button "#edit-save"
    And I add "Trading Name Add" to the inputfield "#edit-trading-name"
    And I click on the button "#edit-save"
    Then I expect that element "#par-flow-transition-business-details" contains the text "Trading Name Add"
    And I click on the checkbox "#edit-confirmation"
    And I click on the button "#edit-save"

      # PARTNERSHIP TASKS

    Then I expect that element "#block-par-theme-content" contains the text "Confirmed by the Organisation"
    And I expect that element ".table-scroll-wrapper" not contains the text "Invite the business to confirm their details"
    And I expect that element ".table-scroll-wrapper" not contains the text "Review and confirm your inspection plan"
    And I expect that element ".table-scroll-wrapper" not contains the text "Review and confirm your documentation"

