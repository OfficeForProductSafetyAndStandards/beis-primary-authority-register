<?php

namespace Drupal\par_subscriptions;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_subscriptions\Entity\ParSubscriptionList;

/**
 * Manages all subscriptions.
 */
class ParSubscriptionManager implements ParSubscriptionManagerInterface {

  use StringTranslationTrait;

  const SUBSCRIPTION_ENTITY = 'par_subscription';
  const SUBSCRIPTION_TYPE_ENTITY = 'par_subscription_list';
  const SUBSCRIPTION_STATUS_SUBSCRIBED = 'subscribed';
  const SUBSCRIPTION_STATUS_VERIFIED = 'verified';
  const UNIVERSAL_UNSUBSCRIBE_CODE = 'unsubscribed';

  const VERIFICATION_EXPIRY = 604800;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The email validator service.
   *
   * @var \Drupal\Component\Utility\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * The drupal messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a subscription manager instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity bundle info service.
   * @param \Drupal\Component\Utility\EmailValidatorInterface $email_validator
   *   The email validator service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info, EmailValidatorInterface $email_validator, MessengerInterface $messenger) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->emailValidator = $email_validator;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritDoc}
   */
  #[\Override]
  public function getLists() {
    $list_entities = ParSubscriptionList::loadMultiple();
    return array_keys($list_entities);
  }

  /**
   *
   */
  public function getListName($list) {
    if ($this->isValidList($list)) {
      $list_type = $subscriptions = $this->entityTypeManager
        ->getStorage(self::SUBSCRIPTION_TYPE_ENTITY)
        ->load($list);

      return $list_type ? $list_type->label() : '';
    }
  }

  /**
   *
   */
  public function createSubscription($list, $email) {
    if ($this->isValidList($list) && $this->isValidEmail($email)) {
      // Check that this email address isn't already on this list.
      if ($subscription = $this->getSubscriptionByEmail($list, $email)) {
        return $subscription;
      }

      $values = [
        'list' => $list,
        'code' => $this->generateCode(),
        'email' => $email,
      ];

      $subscription = $this->entityTypeManager
        ->getStorage(self::SUBSCRIPTION_ENTITY)
        ->create($values);

      return $subscription;
    }

    return NULL;
  }

  /**
   * Get all subscriptions belonging to a list.
   */
  public function getListSubscriptions($list) {
    if ($this->isValidList($list)) {
      $subscriptions = $this->entityTypeManager
        ->getStorage(self::SUBSCRIPTION_ENTITY)
        ->loadByProperties([
          'list' => $list,
          'verified' => 1,
        ]);
    }

    return $subscriptions ?? [];
  }

  /**
   * Get all emails belonging to a list.
   */
  public function getListEmails($list) {
    $subscriptions = $this->getListSubscriptions($list);

    $emails = [];
    foreach ($subscriptions as $i => $subscription) {
      $emails[$i] = $subscription->getEmail();
    }

    return $emails;
  }

  /**
   * Get a subscription by the subscription code.
   *
   * For security this should be the primary way of retrieving existing subscriptions.
   *
   * @return \Drupal\par_subscriptions\Entity\ParSubscriptionInterface |NULL
   *   A subscription entity if found.
   */
  public function getSubscription($code) {
    if ($this->isValidCode($code)) {
      $subscriptions = $this->entityTypeManager
        ->getStorage(self::SUBSCRIPTION_ENTITY)
        ->loadByProperties([
          'code' => $code,
        ]);

      return !empty($subscriptions) ? current($subscriptions) : NULL;
    }

    return NULL;
  }

  /**
   * Get a subscription by the subscription email address.
   *
   * @return \Drupal\par_subscriptions\Entity\ParSubscription|null
   *   A subscription entity if found.
   */
  public function getSubscriptionByEmail($list, $email) {
    if ($this->isValidList($list) && $this->isValidEmail($email)) {
      $subscriptions = $this->entityTypeManager
        ->getStorage(self::SUBSCRIPTION_ENTITY)
        ->loadByProperties([
          'list' => $list,
          'email' => $email,
        ]);

      return !empty($subscriptions) ? current($subscriptions) : NULL;
    }

    return NULL;
  }

  /**
   * Purge old non-verified subscriptions from all lists.
   */
  public function purge() {
    $query = $this->entityTypeManager
      ->getStorage(self::SUBSCRIPTION_ENTITY)
      ->getQuery()
      ->accessCheck(FALSE);

    // Only query for unverified subscriptions.
    $group = $query->orConditionGroup()
      ->condition('verified', 0, 'IS NULL')
      ->condition('verified', 0, '=');
    $query->condition($group);

    // Only query for old subscriptions.
    $request_time = \Drupal::time()->getRequestTime();
    $expiry = $request_time - self::VERIFICATION_EXPIRY;
    $query->condition('created', $expiry, '<');

    $expired_subscriptions = $query->execute();
    foreach ($expired_subscriptions as $id) {
      $this->entityTypeManager
        ->getStorage(self::SUBSCRIPTION_ENTITY)
        ->delete($id);
    }
  }

  /**
   * Generate a code.
   */
  private function generateCode() {
    $code = Crypt::randomBytesBase64(8);
    if ($this->getSubscription($code)) {
      $code = $this->generateCode();
    }
    return $code;
  }

  /**
   * Validate the code.
   *
   * @param $code
   *
   * @return bool
   */
  private function isValidCode($code) {
    $universal_codes = [self::UNIVERSAL_UNSUBSCRIBE_CODE];
    return isset($code) && !empty($code) && array_search($code, $universal_codes) === FALSE;
  }

  /**
   * Validate the email address.
   *
   * @param $email
   *
   * @return bool
   */
  private function isValidEmail($email) {
    return !empty($email) && $this->emailValidator->isValid($email);
  }

  /**
   * Validate that the list exists.
   *
   * @param $list
   *
   * @return bool
   */
  private function isValidList($list) {
    return !empty($list) && array_search($list, $this->getLists()) !== FALSE;
  }

}
