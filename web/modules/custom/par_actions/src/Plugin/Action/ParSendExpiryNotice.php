<?php

namespace Drupal\par_actions\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Event\ParDataEvent;

/**
 * Send a notice that an entity is due to expire.
 *
 * @Action(
 *   id = "par_send_expiry_notice",
 *   label = @Translation("Send an expiry notice."),
 *   type = "system"
 * )
 */
class ParSendExpiryNotice extends ActionBase {

  public function getCurrentUser() {
    return \Drupal::currentUser();
  }

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity instanceof ParDataEntityInterface) {
      // Dispatch a PAR custom event.
      $event = new ParDataEvent($entity);
      $dispatcher = \Drupal::service('event_dispatcher');
      $dispatcher->dispatch(ParDataEvent::customAction($entity->getEntityTypeId(), 'expiry_notification'), $event);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    // @TODO Implement entity/action checks
    $result = AccessResult::allowed();
    return $return_as_object ? $result : $result->isAllowed();
  }

}
