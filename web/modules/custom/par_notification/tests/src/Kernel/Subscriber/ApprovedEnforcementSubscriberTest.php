<?php

namespace Drupal\Tests\par_notification\Kernel\Subscriber;

use Drupal\Tests\par_notification\Kernel\ParNotificationTestBase;

/**
 * Tests PAR enforcement notification approvals.
 *
 * @group PAR Notification
 */
class ApprovedEnforcementSubscriberTest extends ParNotificationTestBase {

  protected $entity;

  public function getEntity() {
    if (!$this->entity) {
      $this->entity = $this->createEnforcement();
    }

    return $this->entity;
  }

  /**
   * Test new enforcement notification sends to the correct subscribers.
   */
  public function testNewEnforcementSubscribers() {
    // Set up the entity events.
    $this->entityEvent = $this->getMockBuilder('Drupal\Core\Entity\EntityEvent')
      ->setMethods(['getEntity'])
      ->disableOriginalConstructor()
      ->getMock();
    $this->entityEvent
      ->expects($this->any())
      ->method('getEntity')
      ->will($this->returnCallback([$this, 'getEntity']));
    $recipients = $this->new_enforcement_subscriber->getRecipients($this->entityEvent);

    // There should be three primary contacts and 1 contact who has opted in to receive all notifications.
    $this->assertTrue((count($recipients) === 4), t("There are %recipients recipients for new enforcement subscriber test.", ['%recipients' => count($recipients)]));

    // Assert that the notification preferences for secondary contacts are being respected.
    $secondary_contacts = array_filter($recipients, function ($recipient) {
      $expected = ['person_2@primary.com', 'person_3@primary.com', 'person_4@primary.com'];
      return (in_array($recipient->getEmail(), $expected));
    });
    $this->assertTrue((count($secondary_contacts) === 3), t("Secondary contact's notification preferences respected for NewEnforcementSubscriber."));
  }

  /**
   * Test reviewed enforcement notification sends to the correct subscribers.
   */
  public function testReviewedEnforcementSubscribers() {
    // Set up the entity events.
    $this->parDataEvent = $this->getMockBuilder('Drupal\par_data\Event\ParNotificationEventInterface')
      ->setMethods(['getEntity'])
      ->disableOriginalConstructor()
      ->getMock();
    $this->parDataEvent
      ->expects($this->any())
      ->method('getEntity')
      ->will($this->returnCallback([$this, 'getEntity']));
    $recipients = $this->enforcement_reviewed_subscriber->getRecipients($this->parDataEvent);

    // There should be one primary contact (the enforcement officer) and 2 contacts who have opted in to receive all notifications.
    $this->assertTrue((count($recipients) === 3), t("There are %recipients recipients for reviewed enforcement subscriber test.", ['%recipients' => count($recipients)]));

    // Assert that the notification preferences for secondary contacts are being respected.
    $secondary_contacts = array_filter($recipients, function ($recipient) {
      $expected = ['person_2@enforcing.com', 'person_4@enforcing.com'];
      return (in_array($recipient->getEmail(), $expected));
    });
    $this->assertTrue((count($secondary_contacts) === 2), t("Secondary contact's notification preferences respected for ReviewedEnforcementSubscriber."));
  }

  /**
   * Test reviewed enforcement notification sends to the correct subscribers.
   */
  public function testApprovedEnforcementSubscribers() {
    // Set up the entity events.
    $this->parDataEvent = $this->getMockBuilder('Drupal\par_data\Event\ParNotificationEventInterface')
      ->setMethods(['getEntity'])
      ->disableOriginalConstructor()
      ->getMock();
    $this->parDataEvent
      ->expects($this->any())
      ->method('getEntity')
      ->will($this->returnCallback([$this, 'getEntity']));
    $recipients = $this->enforcement_approved_subscriber->getRecipients($this->parDataEvent);

    // There should be one primary contact (the enforcement officer) and 2 contacts who have opted in to receive all notifications.
    $this->assertTrue((count($recipients) === 3), t("There are %recipients recipients for approved enforcement subscriber test.", ['%recipients' => count($recipients)]));
  }
}
