<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEnquiryInterface;
use Drupal\par_data\ParDataException;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParRecipient;

/**
 * This message subscriber should apply to any message that deals
 * with the creation of a new enquiry.
 *
 * @ParMessageSubscriber(
 *   id = "new_enquiry",
 *   title = @Translation("New enquiry"),
 *   status = TRUE,
 *   message = {
 *     "new_general_enquiry",
 *     "new_inspection_feedback",
 *   },
 * )
 */
class NewEnquirySubscriber extends ParMessageSubscriberBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getRecipients(MessageInterface $message): array {
    $recipients = parent::getRecipients($message);

    try {
      /** @var \Drupal\par_data\Entity\ParDataEnquiryInterface $enquiries */
      $enquiries = $this->getMessageHandler()->getPrimaryData($message);
      /** @var \Drupal\par_data\Entity\ParDataPartnership[] $partnerships */
      $partnerships = [];

      foreach ($enquiries as $enquiry) {
        if ($enquiry->hasField('field_partnership')
          && !$enquiry->get('field_partnership')->isEmpty()) {
          $partnerships = array_merge(
            $partnerships,
            $enquiry->get('field_partnership')->referencedEntities(),
          );
        }
      }
    }
    catch (ParNotificationException | ParDataException) {
      return $recipients;
    }

    foreach ($partnerships as $partnership) {
      // This message should be sent to the primary authority contacts for the
      // partnership, if no partnership is associated with this enquiry then no
      // recipients will receive this message.
      /** @var \Drupal\par_data\Entity\ParDataPersonInterface $people */
      $people = $partnership->getAuthorityPeople();
      foreach ($people as $key => $person) {
        $recipients[] = new ParRecipient(
          $person->getEmail(),
          $person->getFirstName(),
          $person
        );
      }
    }

    return $recipients;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getSubscribedEntities(MessageInterface $message): array {
    $subscriptions = parent::getSubscribedEntities($message);

    try {
      /** @var \Drupal\par_data\Entity\ParDataEnquiryInterface $enquiry_entities [] */
      $enquiry_entities = $this->getMessageHandler()->getPrimaryData($message);
    }
    catch (ParNotificationException $e) {
      return $subscriptions;
    }

    foreach ($enquiry_entities as $enquiry_entity) {
      if ($enquiry_entity instanceof ParDataEnquiryInterface) {
        // This message should be viewed by the enforcing authority
        // and by the enforced organisation.
        try {
          $sender = [$enquiry_entity->sender()];
        }
        catch (ParDataException $e) {
          $sender = [];
        }
        try {
          $receiver = $enquiry_entity->receiver();
        }
        catch (ParDataException) {
          $receiver = [];
        }
        $subscriptions = array_merge(
          $subscriptions,
          $sender,
          $receiver,
        );
      }
    }

    return $subscriptions;
  }

}
