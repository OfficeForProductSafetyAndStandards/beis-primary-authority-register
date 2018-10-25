<?php

namespace Drupal\Tests\par_notification\Kernel\Subscriber;

use Drupal\Tests\par_notification\Kernel\ParNotificationTestBase;

/**
 * Tests PAR partnership notifications.
 *
 * @group PAR Notification
 */
class NewPartnershipSubscriberTest extends ParNotificationTestBase {

  protected $entity;

  public function getEntity() {
    if (!$this->entity) {
      $this->entity = $this->createPartnership();
    }

    return $this->entity;
  }

  /**
   * Test new partnership notification sends to the correct subscribers.
   */
  public function testNewPartnershipSubscribers() {
    // Set up the entity events.
    $this->entityEvent = $this->getMockBuilder('Drupal\Core\Entity\EntityEvent')
      ->setMethods(['getEntity'])
      ->disableOriginalConstructor()
      ->getMock();
    $this->entityEvent
      ->expects($this->any())
      ->method('getEntity')
      ->will($this->returnCallback([$this, 'getEntity']));
    $recipients = $this->new_partnership_subscriber->getRecipients($this->entityEvent);

    // There should be three primary contacts on the partnership and 1 contact who has opted in to receive all notifications.
    $this->assertTrue((count($recipients) === 4), t("There are %recipients recipients for new partnership subscriber test.", ['%recipients' => count($recipients)]));

    // Assert that the notification preferences for secondary contacts are being respected.
    $secondary_contacts = array_filter($recipients, function ($recipient) {
      $expected = ['person_7@organisation.com', 'person_8@organisation.com', 'person_9@organisation.com'];
      return (in_array($recipient->getEmail(), $expected));
    });
    $this->assertTrue((count($secondary_contacts) === 3), t("Secondary contact's notification preferences respected for NewPartnershipSubscriber."));
  }

  /**
   * Test partnership completion notification sends to the correct subscribers.
   */
  public function testPartnershipCompletedSubscribers() {
    // Set up the entity events.
    $this->parDataEvent = $this->getMockBuilder('Drupal\par_data\Event\ParDataEventInterface')
      ->setMethods(['getEntity'])
      ->disableOriginalConstructor()
      ->getMock();
    $this->parDataEvent
      ->expects($this->any())
      ->method('getEntity')
      ->will($this->returnCallback([$this, 'getEntity']));
    $recipients = $this->partnership_completed_subscriber->getRecipients($this->parDataEvent);

    // There should be three primary contacts on the partnership and 1 contact who has opted in to receive all notifications.
    $this->assertTrue((count($recipients) === 4), t("There are %recipients recipients for partnership completed test.", ['%recipients' => count($recipients)]));

    // Assert that the notification preferences for secondary contacts are being respected.
    $secondary_contacts = array_filter($recipients, function ($recipient) {
      $expected = ['person_2@primary.com', 'person_3@primary.com', 'person_4@primary.com'];
      return (in_array($recipient->getEmail(), $expected));
    });
    $this->assertTrue((count($secondary_contacts) === 3), t("Secondary contact's notification preferences respected for PartnershipApplicationCompletedSubscriber."));
  }

  /**
   * Test partnership approval notification sends to the correct subscribers.
   */
  public function testPartnershipApprovedSubscribers() {
    // Set up the entity events.
    $this->parDataEvent = $this->getMockBuilder('Drupal\par_data\Event\ParDataEventInterface')
      ->setMethods(['getEntity'])
      ->disableOriginalConstructor()
      ->getMock();
    $this->parDataEvent
      ->expects($this->any())
      ->method('getEntity')
      ->will($this->returnCallback([$this, 'getEntity']));
    $recipients = $this->partnership_approved_subscriber->getRecipients($this->parDataEvent);

    // There should be six primary contacts on the partnership (three authority and three organisation)
    // and 2 contacts who has opted in to receive all notifications (1 authority and 1 organisation).
    $this->assertTrue((count($recipients) === 8), t("There are %recipients recipients for partnership approval test.", ['%recipients' => count($recipients)]));

    // Assert that the notification preferences for secondary contacts are being respected.
    $secondary_contacts = array_filter($recipients, function ($recipient) {
      $expected = ['person_2@primary.com', 'person_3@primary.com', 'person_4@primary.com', 'person_7@organisation.com', 'person_8@organisation.com', 'person_9@organisation.com'];
      return (in_array($recipient->getEmail(), $expected));
    });
    $this->assertTrue((count($secondary_contacts) === 6), t("Secondary contact's notification preferences respected for PartnershipApprovedSubscriber."));
  }

  /**
   * Test partnership revocation notification sends to the correct subscribers.
   */
  public function testPartnershipRevocationSubscribers() {
    // Set up the entity events.
    $this->parDataEvent = $this->getMockBuilder('Drupal\par_data\Event\ParDataEventInterface')
      ->setMethods(['getEntity'])
      ->disableOriginalConstructor()
      ->getMock();
    $this->parDataEvent
      ->expects($this->any())
      ->method('getEntity')
      ->will($this->returnCallback([$this, 'getEntity']));
    $recipients = $this->partnership_revoked_subscriber->getRecipients($this->parDataEvent);

    // There should be six primary contacts on the partnership (three authority and three organisation)
    // and 2 contacts who has opted in to receive all notifications (1 authority and 1 organisation).
    $this->assertTrue((count($recipients) === 8), t("There are %recipients recipients for partnership revocation test.", ['%recipients' => count($recipients)]));

    // Assert that the notification preferences for secondary contacts are being respected.
    $secondary_contacts = array_filter($recipients, function ($recipient) {
      $expected = ['person_2@primary.com', 'person_3@primary.com', 'person_4@primary.com', 'person_7@organisation.com', 'person_8@organisation.com', 'person_9@organisation.com'];
      return (in_array($recipient->getEmail(), $expected));
    });
    $this->assertTrue((count($secondary_contacts) === 6), t("Secondary contact's notification preferences respected for PartnershipRevocationSubscriber."));
  }
}
