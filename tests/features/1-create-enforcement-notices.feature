Feature: Enforcement Officer - Enforcement Notice Process

    @ci
    Scenario Outline: Raise an enforcement notice against a coordinated partnership
        Given I am logged in as "par_enforcement_officer@example.com"
        When I search for a partnership between "<Authority>" and "<Organisation>"
        And I raise a new enforcement against a direct partnership
        And I choose a coordinated member to enforce
        And I enter a legal entity to enforce
        And I enter the details of a proposed enforcement
        And I add an enforcement action "<Notification Title>"
        And I save the enforcement notice
        Then the "enforcement creation" email confirmations for "<PARUser>" are processed
    Examples:
        | Notification Title   | Organisation   | Authority                       | PARUser                   |
        | Enforcement notice 1 | Charlie's      | Upper West Side Borough Council | par_authority@example.com |
        | Enforcement notice 2 | Charlie's      | Upper West Side Borough Council | par_authority@example.com |

    @ci
    Scenario Outline: Raise an enforcement notice against a direct partnership
        Given I am logged in as "par_enforcement_officer@example.com"
        When I search for a partnership between "<Authority>" and "<Organisation>"
        And I raise a new enforcement against a direct partnership
        And I choose an existing legal entity to enforce
        And I enter the details of a proposed enforcement
        And I add an enforcement action "<Notification Title>"
        And I save the enforcement notice
        Then the "enforcement creation" email confirmations for "<PARUser>" are processed
    Examples:
        | Notification Title   | Organisation   | Authority                       | PARUser                   |
        | Enforcement notice 3 | Charlie's      | Lower East Side Borough Council | par_authority@example.com |
        | Enforcement notice 4 | Charlie's      | Lower East Side Borough Council | par_authority@example.com |

    @ci-pending
    Scenario: Raise an enforcement notice against a coordinated partnership with multiple actions
        Given I am logged in as "par_enforcement_officer@example.com"
        When I search for a partnership between "Lower East Side Borough Council" and "Charlie's Cafe"
        And I click the link text "Send a notification of a proposed enforcement action"
        And the element "h1.heading-xlarge" contains the text "Have you discussed this issue with the Primary Authority?"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge" contains the text "Which authority are you acting on behalf of?"
        And the element "#par-enforce-organisation" contains the text "Choose the member to enforce"
        When I click on the radio "input[name=\"par_data_organisation_id\"]"
        And I click on the button "#edit-next"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge" contains the text "Raise notice of enforcement action"
        When I click on the radio "#edit-legal-entities-select-add-new"
        And I add "New Legal Entity 4" to the inputfield "#edit-alternative-legal-entity"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Raise notice of enforcement action"
        And the element "#edit-enforced-organisation" contains the text "Legal Entity 4"
        When I add "601" random chars of text to field "#edit-summary"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Raise notice of enforcement action"
        And the element "h1.heading-xlarge" contains the text "Add an action to the enforcement notice"
        When I add "Multiple Action 1" to the inputfield "#edit-par-component-enforcement-action-0-title"
        And I click on the button "#edit-par-component-enforcement-action-0-regulatory-function-14"
        And I add "601" random chars of text to field "#edit-par-component-enforcement-action-0-details"

        # ADD ANOTHER ACTION

        And I click the link text "Add another"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Raise notice of enforcement action"
        When I add "Multiple Action 2" to the inputfield "#edit-par-component-enforcement-action-1-title"
        And I click on the button "#edit-par-component-enforcement-action-1-regulatory-function-14"
        And I add "601" random chars of text to field "#edit-par-component-enforcement-action-1-details"
        And I click on the button "#edit-next"

        Then the element "#par-enforcement-notice-raise-review" contains the text "Multiple Action 1"
        And the element "#par-enforcement-notice-raise-review" contains the text "Multiple Action 2"
        And the element "h1.heading-xlarge" contains the text "Review the enforcement notice"
        And the element "#edit-enforced-organisation" contains the text "Legal Entity 4"

        # EDIT AT REVIEW STAGE

        And I click the link text "Change the enforced organisation"
        And I click on the radio "#edit-legal-entities-select-add-new"
        When I add "Change to enforced organisation" to the inputfield "#edit-alternative-legal-entity"
        And I click on the button "#edit-next"
        And the element "#edit-enforced-organisation" contains the text "Change to enforced organisatio"

        When I click the link text "Change the summary of this enforcement"
        And I add "some additional summary changes" to the inputfield "#edit-summary"
        And I click on the button "#edit-next"
        And the element "#edit-enforcement-notice" contains the text "some additional summary changes"

        When I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Enforcement notice sent"
        When I click on the button ".button"
        And I open the path "/user/logout"

    @ci
    Scenario: Enforce all members using a paginated list
        Given I am logged in as "par_enforcement_officer@example.com"
        And I click the link text "Search for a partnership"
        When I add "Member Upload Test Business" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        And I click the link text "Partnership between Upper West Side Borough Council and Member Upload Test Business"
        When I click the link text "Send a notification of a proposed enforcement action"
        And the element "h1.heading-xlarge" contains the text "Have you discussed this issue with the Primary Authority?"
        And I click on the button "#edit-next"
        And the element "h1.heading-xlarge" contains the text "Enforce member"
        And I see "10" occurences of element "#edit-par-data-organisation-id--wrapper > .multiple-choice"
        And the element ".pagerer-pager-basic" does exist
        And I see "3" occurences of element ".pagerer-right-pane .pager__items > .pager__item"

    @ci
    Scenario: Issue enforcement notice on Coordinated Partnership with no members
        Given I am logged in as "par_enforcement_officer@example.com"
        And I click the link text "Search for a partnership"
        When I add "Charity Retail Association" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        And I click the link text "Partnership between Salford City Council and Charity Retail Association"
        When I click the link text "Send a notification of a proposed enforcement action"
        And the element "h1.heading-xlarge" contains the text "Have you discussed this issue with the Primary Authority?"
        And I click on the button "#edit-next"
        And I click on authority selection if available
        When I click on the button "#edit-next"
        And I add "Added Legal Entity" to the inputfield "#edit-alternative-legal-entity"
        And I click on the button "#edit-next"
        And I click on the button "#edit-notice-type-proposed"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Raise notice of enforcement action"
        And the element "#edit-enforced-organisation" contains the text "Added Legal Entity"
        When I add "601" random chars of text to field "#edit-summary"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Raise notice of enforcement action"
        And the element "h1.heading-xlarge" contains the text "Add an action to the enforcement notice"
        When I add "An Action 1" to the inputfield "#edit-par-component-enforcement-action-0-title"
        And I click on the button "#edit-par-component-enforcement-action-0-regulatory-function-14"
        And I add "601" random chars of text to field "#edit-par-component-enforcement-action-0-details"
        And I click on the button "#edit-next"
        Then the element "#par-enforcement-notice-raise-review" contains the text "An Action 1"
        And the element "h1.heading-xlarge" contains the text "Review the enforcement notice"
        And the element "#edit-enforced-organisation" contains the text "Added Legal Entity"
        When I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Enforcement notice sent"
