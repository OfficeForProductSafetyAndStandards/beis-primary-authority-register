
Feature: PA User - Messaging

        @1253 @1254 @pending
        Scenario Outline: Enquiry Messages View
                Given I am logged on as "par_authority@example.com"
                And I click on the link text "See enquiries"
                Then the element "h3" contains the text "Authority Enquiries"
                # And I select the option with the value "<MessageType>" for element "#message-types"
                # Then only messages of type "<MessageType>" are displayed

        Examples:
                | MessageType     |
                | General Enquiry |

        @1256 @pending
        Scenario: Send Message Confirmation Page
                Given I am logged on to the system as "a.user@example.com"
                When I submit a validated message
                Then the element "h3" contains the text "Message success"
