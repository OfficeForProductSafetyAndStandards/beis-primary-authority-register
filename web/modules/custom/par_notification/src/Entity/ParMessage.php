<?php

namespace Drupal\par_notification\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines the PAR Form Flow entity.
 *
 * @ConfigEntityType(
 *   id = "par_message",
 *   label = @Translation("PAR Message"),
 *   config_prefix = "par_message",
 *   handlers = {
 *     "storage" = "Drupal\Core\Config\Entity\ConfigEntityStorage",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\Core\Entity\EntityForm",
 *       "edit" = "Drupal\Core\Entity\EntityForm",
 *       "delete" = "Drupal\Core\Entity\EntityConfirmFormBase"
 *     }
 *   },
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/par/messages/{par_entity_type}",
 *     "edit-form" = "/admin/config/par/messages/{par_entity_type}/edit",
 *     "delete-form" = "/admin/config/par/messages/{par_entity_type}/delete",
 *     "collection" = "/admin/config/par/messages"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "subject",
 *     "message"
 *   }
 * )
 */
class ParMessage extends ConfigEntityBase implements ParMessageInterface {

  use StringTranslationTrait;

  /**
   * The message ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The administrative label for the message.
   *
   * @var string
   */
  protected $label;

  /**
   * The message subject or heading.
   *
   * @var string
   */
  protected $subject;

  /**
   * The message body.
   *
   * @var string
   */
  protected $message;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $values, $entity_type) {
    parent::__construct($values, $entity_type);
  }

  /**
   * {@inheritdoc}
   */
  public function getSubject() {
    return $this->subject;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessage() {
    return $this->message;
  }

  /**
   * {@inheritdoc}
   */
  public function setSubject($subject) {
    return $this->subject = $subject;
  }

  /**
   * {@inheritdoc}
   */
  public function setMessage($message) {
    return $this->message = $message;
  }
}
