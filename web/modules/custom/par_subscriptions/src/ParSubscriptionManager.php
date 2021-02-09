<?php

namespace Drupal\par_subscriptions;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Manages all subscriptions
 */
class ParSubscriptionManager implements ParSubscriptionManagerInterface {

  use StringTranslationTrait;

  const SUBSCRIPTION_ENTITY_TYPE = 'par_subscription';
  const SUBSCRIPTION_STATUS_SUBSCRIBED = 'subscribed';
  const SUBSCRIPTION_STATUS_VERIFIED = 'verified';
  const UNIVERSAL_UNSUBSCRIBE_CODE = 'unsubscribed';

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
   * @var EmailValidatorInterface
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
   *   The messenger service
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
  public function getLists() {
    $bundle_info = $this->entityTypeBundleInfo->getBundleInfo(self::SUBSCRIPTION_ENTITY_TYPE);

    return $bundle_info ? array_keys($bundle_info) : [];
  }

  public function createSubscription($list, $email) {
    if ($this->isValidList($list) && $this->isValidEmail($email)) {
      // Check that this email address isn't already on this list.
      if ($subscription = $this->getSubscriptionByEmail($email)) {
        return $subscription;
      }

      $values = [
        'list' => $list,
        'code' => $this->generateCode(),
        'email' => $email,
      ];

      $subscription = $this->entityTypeManager
        ->getStorage(self::SUBSCRIPTION_ENTITY_TYPE)
        ->create($values);

      return $subscription;
    }

    return NULL;
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
        ->getStorage(self::SUBSCRIPTION_ENTITY_TYPE)
        ->loadByProperties([
          'code' => $code
        ]);

      return !empty($subscriptions) ? current($subscriptions) : NULL;
    }

    return NULL;
  }

  /**
   * Get a subscription by the subscription email address.
   *
   * @return \Drupal\par_subscriptions\Entity\ParSubscription|NULL
   *   A subscription entity if found.
   */
  public function getSubscriptionByEmail($email) {
    if ($this->isValidEmail($email)) {
      $subscriptions = $this->entityTypeManager
        ->getStorage(self::SUBSCRIPTION_ENTITY_TYPE)
        ->loadByProperties([
          'email' => $email
        ]);

      return !empty($subscriptions) ? current($subscriptions) : NULL;
    }

    return NULL;
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
    return isset($code) && !empty($code) && array_search($code, $universal_codes) === false;
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
    return !empty($list) && array_search($list, $this->getLists()) !== false;
  }
}
