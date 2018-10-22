<?php

namespace Drupal\Tests\par_notification\Kernel\Subscriber;

use Drupal\Tests\par_notification\Kernel\ParNotificationTestBase;

/**
 * Tests PAR Enforcement Notice entity.
 *
 * @group PAR Notification
 */
class NewEnforcementSubscriberTest extends ParNotificationTestBase {

  protected $entity;

  public function getEntity() {
    if (!$this->entity) {
      $this->entity = $this->createEnforcement();
    }

    return $this->entity;
  }

  /**
   * Test to validate an authority entity.
   */
  public function testNewEnforcementSubscribers() {
    $new_enforcement_subscriber = $this->getMockBuilder('Drupal\par_notification\EventSubscriber\NewEnforcementSubscriber')->disableOriginalConstructor()->getMock();
    $recipients = $new_enforcement_subscriber->getRecipients($this->entityEvent);
var_dump($this->entityEvent->getEntity()->label());
    $this->assertTrue((count($recipients) === 2), t("There are %recipients recipients for new enforcement subscriber test.", ['%recipients' => count($recipients)]));
  }
}
