<?php

namespace Drupal\audit_log\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Audit log entities.
 *
 * @ingroup audit_log
 */
interface AuditLogInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Audit log creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Audit log.
   */
  public function getCreatedTime();

  /**
   * Sets the Audit log creation timestamp.
   *
   * @param int $timestamp
   *   The Audit log creation timestamp.
   *
   * @return \Drupal\audit_log\Entity\AuditLogInterface
   *   The called Audit log entity.
   */
  public function setCreatedTime($timestamp);

}
