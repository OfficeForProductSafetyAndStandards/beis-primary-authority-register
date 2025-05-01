<?php

namespace Drupal\par_notification;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * A class that defines relationships.
 */
class ParRecipient implements \Stringable {

  const DEFAULT_ADDRESSABLE_NAME = 'Subscriber';

  /**
   * Constructs an instance of a PAR Recipient class.
   *
   * @param ?EntityInterface $entity
   *   The related entity, usually a user or contact record.
   */
  public function __construct(
      /**
       * The email address for this recipient.
       */
      protected string $email,
      /**
       * The addressable name for this recipient.
       */
      protected ?string $name = NULL,
      protected ?EntityInterface $entity = NULL
  )
  {
  }

  /**
   * Get the user storage.
   */
  public function getUserStorage() {
    return \Drupal::service('entity_type.manager')->getStorage('user');
  }

  /**
   * The string representation of the recipient should be the email address.
   */
  #[\Override]
  public function __toString(): string {
    return $this->getEmail();
  }

  /**
   * Get the related entity.
   *
   * @return ?EntityInterface
   *   An entity if it exists.
   */
  public function getEntity(): ?EntityInterface {
    return $this->entity;
  }

  /**
   * If there is a user account associated with this recipient return it.
   *
   * @return AccountInterface
   *   A user account if it exists.
   */
  public function getAccount(): AccountInterface {
    // If the recipient entity is a user, return it.
    if ($this->getEntity() instanceof UserInterface) {
      $user = $this->getEntity();
    }
    // If the recipient entity is a ParDataPerson,
    // return the associated user account, or skip if there is none.
    else if ($this->getEntity() instanceof ParDataPersonInterface) {
      $user = $this->getEntity()->retrieveUserAccount();
    }

    // As a backup try to load a user account given the email provided.
    if (empty($user)) {
      $users = $this->getUserStorage()
        ->loadByProperties(['mail' => $this->getEmail()]);
      $user = current($users) instanceof UserInterface ? current($users) : NULL;
    }

    // Return an account object.
    return $user instanceof UserInterface ?
      $this->getUserStorage()->load($user->id()) : new AnonymousUserSession();
  }

  /**
   * Get the email address for this recipient.
   *
   * @return string
   *   The email address.
   */
  public function getEmail(): string {
    return $this->email;
  }

  /**
   * Get the name of this recipient.
   *
   * @return string
   *   The email address.
   */
  public function getName(): string {
    return $this->name ?? self::DEFAULT_ADDRESSABLE_NAME;
  }
}
