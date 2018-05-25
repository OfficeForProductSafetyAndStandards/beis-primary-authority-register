<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\message\Entity\Message;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReviewedEnforcementSubscriber implements EventSubscriberInterface {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_enforcement_notification
   */
  const MESSAGE_ID = 'reviewed_enforcement';

  /**
   * The notication plugin that will deliver these notification messages.
   */
  const DELIVERY_METHOD = 'plain_email';

  protected $recipients = [];

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events[ParDataEvent::statusChange('par_data_enforcement_notice', 'reviewed')][] = ['onEnforcementReview', 800];

    return $events;
  }

  /**
   * Get the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public function getEntityTypeManager() {
    return \Drupal::entityTypeManager();
  }

  /**
   * Get the notification service.
   *
   * @return mixed
   */
  public function getNotifier() {
    return \Drupal::service('message_notify.sender');
  }

  /**
   * Get the current user sending the message.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   */
  public function getCurrentUser() {
    return \Drupal::currentUser();
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEnforcementReview(ParDataEvent $event) {
    /** @var ParDataEntityInterface $par_data_enforcement_notice */
    $par_data_enforcement_notice = $event->getEntity();

    // Load the message template.
    $template_storage = $this->getEntityTypeManager()->getStorage('message_template');
    $message_template = $template_storage->load(self::MESSAGE_ID);

    $message_storage = $this->getEntityTypeManager()->getStorage('message');

    if (!$message_template || !$par_data_enforcement_notice) {
      // @TODO Log that the template couldn't be loaded.
      return;
    }

    // Get the link to approve this notice.
    $options = ['absolute' => TRUE];
    $enforcement_url = Url::fromRoute('par_enforcement_send_flows.send_enforcement', ['par_data_enforcement_notice' => $par_data_enforcement_notice->id()], $options);

    // Notify all relevant users at the primary authority.
    $primary_authority = $par_data_enforcement_notice->getPrimaryAuthority(TRUE);

    foreach ($primary_authority->getPerson() as $person) {
      // Notify all users in this authority with the appropriate permissions.
      if (($account = $person->getUserAccount()) && $person->getUserAccount()->hasPermission('approve enforcement notice')
        && !isset($this->recipients[$account->id()])) {

        // Record the recipient so that we don't send them the message twice.
        $this->recipients[$account->id()] = $account->getEmail();

        // Create one message per user.
        $message = $message_storage->create([
          'template' => $message_template->id()
        ]);

        // Add contextual information to this message.
        if ($message->hasField('field_enforcement_notice')) {
          $message->set('field_enforcement_notice', $par_data_enforcement_notice);
        }

        // Add some custom arguments to this message.
        $message->setArguments([
          '@enforcement_notice_view' => $enforcement_url->toString(),
        ]);

        // The owner is the user who this message belongs to.
        if ($account) {
          $message->setOwnerId($account->id());
        }
        $message->save();

        // The e-mail address can be overridden if we don't want
        // to send to the message owner set above.
        $options = [
          'mail' => $account->getEmail(),
        ];

        $this->getNotifier()->send($message, $options, self::DELIVERY_METHOD);
      }
    }
  }

}
