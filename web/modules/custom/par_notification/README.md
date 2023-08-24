# Notifications
The Primary Authority Register uses the [Message Stack](https://www.drupal.org/node/2180145) set of modules to send messages to users, either by email, or by displaying them on the user's dashboard.

The terms 'message' and 'notification' are often used interchangeably.

## Understanding Notifications
Notifications are sent to users when something happens, they are all in response to an action completed by another user, or to a change of the data.

This data is recorded alongside the message, and used to dynamically replace values, and calculate where to redirect the user to when they interact with the notification.

This interaction is managed by adding a 'Primary Action Link' to the notification that will direct the user to the page that best displays this information or that allows the user to complete an action related to this information.

### Informational Notifications vs Task Notifications
The majority of the notifications within PAR are informational only, and alert the user to something they might want to be aware of.

Tasks, on the other hand, are notifications that have an action that needs to be completed by the user, e.g. a partnership that needs to be approved.

## Create a new Notification
There are a few steps to create a new notification:

- Create a message template (see the message module & [Message Partials](#message-partials))
- Add the required fields to the message template (see [Message Fields](#message-fields))
- Configure the display of the message fields
  - Default & Full content: should display all message partials except those with the primary action link
  - Notify - Email body: should display all message partials
  - Notify - Email subject: should display the field_subject _only_
  - Summary: should display message partial 0 _only_
- Create a new Notification Event (event subscriber) to create the notification
- Create a new ParMessageSubscriber plugin that dictates how the message should get its subscribers (users and par entities)
- Create a new ParLinkAction plugin that determines how the Primary Action Link for this message should be redirected
- If the ParLinkAction is a 'task' that needs to be completed, create an additional event subscriber that acts on the completion of this action and expires the message (@see message_expiry module)
- Ensure all users who should see the notification have the 'Receive MESSAGE_TEMPLATE_ID notifications' permission
- Add a key for the message template id into ParMessageHandler::getPrimaryField() and ParMessageHandler::getMessageGroup() for added functionality

### Notification Events
Event subscribers are responsible for reaching to the action completed by a user or a change in the data, and to send out a notification in response

**Note:** As a rule event subscribers should be named so that they correspond with the message_template that they send out.

### Message Partials
The body text of the message is broken apart into [Message Partials](https://git.drupalcode.org/project/message/-/blob/8.x-1.x/README.md#partials) or message segments (see the Message module README.md for more information). Not all partials are displayed in all contexts.

The first **partial 0** should be reserved for the most important part of the notification, this should be limited to one short sentence so that it can be shown in _all_ contexts (including in summary lists), and should not include any links.

All other partials can be configured as required for each display mode.

**Note:** The core of the message, **partial 0**, should be displayed in all view modes and contexts.

### Message Fields
There are some standard fields which should be added to all message templates:

- field_to
- field_subject

All other messages will likely include an entity reference field which records the data that is related to the message, for example field_partnership.

## Modules
* Message
* Message Notify - allows messages to be sent to users by email
* Message Digest - allows messages to be grouped and sent less frequently in a digest
* Message Expire - allows messages that are no longer useful to be expired and hidden from the user

## Par Notifications (module)
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

