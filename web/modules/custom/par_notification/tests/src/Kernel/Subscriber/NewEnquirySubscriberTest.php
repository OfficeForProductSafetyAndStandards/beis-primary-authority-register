<?php

namespace Drupal\Tests\par_notification\Kernel\Subscriber;

use Drupal\Tests\par_notification\Kernel\ParNotificationTestBase;

/**
 * Tests PAR enquiry notifications.
 *
 * @group PAR Notification
 */
class NewEnquirySubscriberTest extends ParNotificationTestBase {

  protected $entity;

  public function getEntity() {
    if (!$this->entity) {
      $this->entity = $this->createGeneralEnquiry();
    }

    return $this->entity;
  }

  public function getComment() {
    if (!$this->comment) {
      $this->comment = $this->createGeneralEnquiryComment();
    }

    return $this->comment;
  }

  /**
   * Test new enquiry notification sends to the correct subscribers.
   */
  public function testNewEnquirySubscribers() {
    // Set up the entity events.
    $this->entityEvent = $this->getMockBuilder('Drupal\Core\Entity\EntityEvent')
      ->setMethods(['getEntity'])
      ->disableOriginalConstructor()
      ->getMock();
    $this->entityEvent
      ->expects($this->any())
      ->method('getEntity')
      ->will($this->returnCallback([$this, 'getEntity']));
    $recipients = $this->new_enquiry_subscriber->getRecipients($this->entityEvent);

    // There should be three primary contacts and 1 contact who has opted in to receive all notifications.
    $this->assertTrue((count($recipients) === 4), t("There are %recipients recipients for new enquiry subscriber test.", ['%recipients' => count($recipients)]));

    // Assert that the notification preferences for secondary contacts are being respected.
    $secondary_contacts = array_filter($recipients, function ($recipient) {
      $expected = ['person_2@primary.com', 'person_3@primary.com', 'person_4@primary.com'];
      return (in_array($recipient->getEmail(), $expected));
    });
    $this->assertTrue((count($secondary_contacts) === 3), t("Secondary contact's notification preferences respected for NewGeneralEnquirySubscriber."));
  }

//  /**
//   * Test enquiry response notification sends to the correct subscribers.
//   */
//  public function testEnquiryResponseSubscribers() {
//    // Set up the entity events.
//    $this->entityEvent = $this->getMockBuilder('Drupal\Core\Entity\EntityEvent')
//      ->setMethods(['getEntity'])
//      ->disableOriginalConstructor()
//      ->getMock();
//    $this->entityEvent
//      ->expects($this->any())
//      ->method('getEntity')
//      ->will($this->returnCallback([$this, 'getComment']));
//    $recipients = $this->new_enquiry_response_subscriber->getRecipients($this->entityEvent);
//
//    // There should be three primary contacts (for the primary authority)
//    // and two secondary contacts (for the enforcing authority) who have opted in to receive all notifications.
//    $this->assertTrue((count($recipients) === 6), t("There are %recipients recipients for new enforcement subscriber test.", ['%recipients' => count($recipients)]));
//
//    // Assert that the notification preferences for secondary contacts are being respected.
//    $secondary_contacts = array_filter($recipients, function ($recipient) {
//      $expected = ['person_2@primary.com', 'person_3@primary.com', 'person_4@primary.com', 'person_2@enforcing.com', 'person_4@enforcing.com'];
//      return (in_array($recipient->getEmail(), $expected));
//    });
//    $this->assertTrue((count($secondary_contacts) === 5), t("Secondary contact's notification preferences respected for NewGeneralEnquiryReplySubscriber."));
//  }
}
