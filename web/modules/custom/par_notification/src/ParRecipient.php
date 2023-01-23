<?php

namespace Drupal\par_notification;

use Drupal\Core\Entity\EntityInterface;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\user\Entity\User;

/**
 * A class that defines relationships.
 */
class ParRecipient {

  const DEFAULT_ADDRESSABLE_NAME = 'Subscriber';

  /**
   * The email address for this recipient.
   */
  protected string $email;

  /**
   * The addressable name for this recipient.
   */
  protected ?string $name;

  /**
   * An optional entity that contains the information about this recipient.
   */
  protected ?EntityInterface $entity;

  /**
   * Constructs an instance of a PAR Recipient class.
   *
   * @param ?EntityInterface $entity
   *   The related entity.
   */
  public function __construct(string $email, string $name = NULL, EntityInterface $entity = NULL) {
    $this->email = $email;
    $this->name = $name;
    $this->entity = $entity;
  }

  /**
   * The string representation of the recipient should be the email address.
   */
  public function __toString() {
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
