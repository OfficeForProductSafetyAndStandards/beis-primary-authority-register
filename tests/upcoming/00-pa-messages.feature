
Feature: PA User - Messaging

# @1253 @pending
# Scenario: Enquiry Messages View
    # Given I am logged on as "par_authority@example.com"
    # And I click on the link text "Enquiries recieved"
    # Then the element "h3" contains the text "Your recieved enquiries"


@ci @pending
Scenario Outline: Enquiry Messages View Filter
        Given I am logged in as "par_authority@example.com"
        When I click the link text "See enforcement notifications sent"
        And I select the option with the value "<MessageType>" for element "#message-types"
        Then only messages of type "<MessageType>" are displayed
Examples:
        | MessageType     |
        | General Enquiry |

# @1256 @pending
# Scenario: Send Message Confirmation Page
# Given I am logged on to the system as user
# When I submit a validated message
# Then the element "h3" contains the text "Message success"
