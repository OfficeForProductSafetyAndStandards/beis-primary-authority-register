# Notifications & Messages
The Primary Authority Register uses the [Message Stack](https://www.drupal.org/node/2180145) set of modules to send messages to users, either by email, or by displaying them on the user's dashboard.

The Message Stack enables messages to be sent to users for a wide range of reasons, and are often used for sending information between users in private messaging features.

In the Primary Authority Register, however, messages are all notifications of actions that have happened within the register.

As such, all messages are notifications, and the terms messages & notifications are often used interchangeably.

It is often broken down by referring to a message as the content (the data or the entity), and referring to a notification as the item received by a user (the display).

## Understanding Notifications
Notifications are sent to users when something happens, they are all in response to an action completed by another user, which in turn is almost always related to a change made to the data.

This data is recorded alongside the message, and used to dynamically replace values, and calculate which link to display to the user.

As such messages can contain a link that will direct the user to the page that best displays this information or that allows the user to complete an action related to this information.

### Informational Notifications vs Task Notifications
The majority of the notifications within PAR are informational only, and alert the user to something they might want to be aware of.

Tasks, on the other hand, are notifications that have an action that needs to be completed by the user, e.g. a partnership that needs to be approved.

### Message Partials
The body text of the message is broken apart into [Message Partials](https://git.drupalcode.org/project/message/-/blob/8.x-1.x/README.md#partials) (see the Message module README.md for more information).

The partials are ordered in a specific way to allow additional information to be displayed depending on how the message is being sent.

The order for partials should be as follows:
* 0 - The personalisation line for the email `Dear !first_name`.
* 1 - The core of the message content.
* _any further partials_ - The ParLinkAction link `[message:primary-action]`, and any additional content blocks.
* _last_ - The sign off line for the email, e.g. `Regards, Primary Authority Register team`

**Note:** When displaying a message within the site only the core of the message needs to be displayed (1).

## Modules
* Message
* Message Notify - allows messages to be sent to users by email
* Message Digest - allows messages to be grouped and sent less frequently in a digest
* Message Expire - allows messages that are no longer useful to be expired and hidden from the user

## Par Notifications
This module builds upon this functionality through the addition of these plugins:
* ParMessageSubscriber
* ParLinkAction

It also adds an email notification plugin for sending plain text emails that can be handled by the GovUK Notify service.

### ParMessageSubscriber
The Message Subscriber plugin is responsible for handling permissions on messages, and for chosing who to send messages to.

### ParLinkAction
Responsible for adding a primary action link to messages using the token `[message:primary-action]`, and for calculating where this should redirect to once clicked on.

The destination of this link is not calculated when it is generated, but instead when the user clicks on the link.

This enables us to better manage situations where a week or more might have passed since the user was sent an email, during which time the link might have expired or the action been completed.

This does mean that fallbacks are required for most messages so that redirection can still occur if the original link is no longer accessible.

#### TaskInterface
The ParTaskInterface can be added to any ParLinkAction plugin to indicate that this link is an action that must be completed by the user.

Any notifications that have an action that implements this interface will be marked as 'Task Notifications'.

