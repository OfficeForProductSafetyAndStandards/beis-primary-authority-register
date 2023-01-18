<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\comment\CommentInterface;
use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\message\Entity\Message;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataGeneralEnquiry;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParEventSubscriberBase;

class NewDeviationRequestReplySubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_deviation_response
   */
  const MESSAGE_ID = 'new_deviation_response';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events[EntityEvents::insert('comment')][] = ['onEvent', 800];

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var CommentInterface $entity */
    $entity = $event->getEntity();
    /** @var ParDataDeviationRequest $commented_entity */
    $commented_entity = $entity->getCommentedEntity();
    $par_data_partnership = $commented_entity?->getPartnership(TRUE);

    // Only send messages for active deviation requests.
    if ($commented_entity instanceof ParDataDeviationRequest &&
      $par_data_partnership instanceof ParDataPartnership &&
      $commented_entity->isActive()) {

      // Send the message.
      $arguments = [
        '@partnership_label' => strtolower($par_data_partnership->label()),
      ];
      $additional_parameters = ['field_deviation_request' => $commented_entity];
      $this->sendMessage($arguments, $additional_parameters);
    }
  }

}
