Feature: Enforcement Officer - Enforcement Notice Process

    @ci @smoke
    Scenario Outline: Raise an enforcement notice against a coordinated partnership
        Given I am logged in as "par_enforcement_officer@example.com"
        When I search for a partnership between "<Authority>" and "<Organisation>"
        And I raise a new enforcement against a partnership
        And I choose a coordinated member to enforce
        And I enter a legal entity to enforce "Member's legal entity"
        And I enter the details of a proposed enforcement
        And I add an enforcement action "<Notification Title>"
        And I review the enforcement notice
        Then the "enforcement creation" email confirmations for "<PARUser>" are processed
    Examples:
        | Notification Title   | Organisation   | Authority                       | PARUser                   |
        | Enforcement notice 1 | Charlie's      | Upper West Side Borough Council | par_authority@example.com |
        | Enforcement notice 2 | Charlie's      | Upper West Side Borough Council | par_authority@example.com |

    @ci @smoke
    Scenario Outline: Raise an enforcement notice against a direct partnership
        Given I am logged in as "par_enforcement_officer@example.com"
        When I search for a partnership between "<Authority>" and "<Organisation>"
        And I raise a new enforcement against a partnership
        And I choose an existing legal entity to enforce
        And I enter the details of a proposed enforcement
        And I add an enforcement action "<Notification Title>"
        And I review the enforcement notice
        Then the "enforcement creation" email confirmations for "<PARUser>" are processed
    Examples:
        | Notification Title   | Organisation   | Authority                       | PARUser                   |
        | Enforcement notice 3 | Charlie's      | Lower East Side Borough Council | par_authority@example.com |
        | Enforcement notice 4 | Charlie's      | Lower East Side Borough Council | par_authority@example.com |

    @ci
    Scenario: Raise an enforcement notice against a direct partnership with multiple actions
        Given I am logged in as "par_enforcement_officer@example.com"
        When I search for a partnership between "Lower East Side Borough Council" and "Charlie's"
        And I raise a new enforcement against a partnership
        And I choose an existing legal entity to enforce
        And I enter the details of a proposed enforcement
        And I add multiple enforcement actions "Multiple Action 1", "Multiple Action 2"
        And I review the enforcement notice
        Then the "enforcement creation" email confirmations for "par_authority@example.com" are processed

    @ci
    Scenario: Enforce all members using a paginated list
        Given I am logged in as "par_enforcement_officer@example.com"
        When I search for a partnership between "Upper West Side Borough Council" and "Member Upload Test Business"
        And I raise a new enforcement against a partnership
        And the element "h1.heading-xlarge" contains the text "Enforce member"
        And I see "10" occurences of element "#edit-par-data-organisation-id--wrapper > .multiple-choice"
        And the element ".pagerer-pager-basic" does exist
        And I see "3" occurences of element ".pagerer-right-pane .pager__items > .pager__item"
