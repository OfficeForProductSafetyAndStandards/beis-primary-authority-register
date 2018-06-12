Feature: Further EN tests

    @pending
    Scenario Outline: Enforcement Officer With More Than One Authority - Issue enforcement notice

        #LOGIN
        
        Given I am logged in as "eo_more_than_one_authority@example.com"
        # When I enforce a direct partnership
        # Then I should get the 'Choose which authority to act on behalf of' screen

    @pending
    Scenario Outline: Enforcement Officer With One Authority - Issue enforcement notice On Direct Partnership

        #LOGIN
        
        Given I am logged in as "enforcement_officer@example.com"
        # When I enforce a direct partnership
        # Then I should NOT get the 'Choose which authority to act on behalf of' screen

    @pending
    Scenario Outline: Enforcement Officer With One Authority - Issue enforcement notice On Coordinated Partnership

        #LOGIN
        
        Given I am logged in as "enforcement_officer@example.com"
        And I go to partnership detail page for my partnership "Organisation For Coordinated Partnership" with status "confirmed_authority"
        # When I enforce a coordinated partnership that has at least two members
        # Then I should get the 'Choose the member to enforce' screen

    @pending
    Scenario Outline: Enforcement Officer With One Authority - Issue enforcement notice On Coordinated Partnership

        #LOGIN
        
        Given I am logged in as "enforcement_officer@example.com"
        # When I enforce a coordinated partnership that has no members
        # Then I should get the 'Choose Legal Entity to enforce' screen (after entering EO's contact details)

    @pending
    Scenario Outline: HD User - Issue enforcement notice On Direct Partnership

        #LOGIN
        
        Given I am logged in as "eo_one_authority@example.com"
        # When I enforce a direct partnership
        # Then I should get the 'Choose Legal Entity to enforce' screen (after entering EO's contact details)
        