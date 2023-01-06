<?php

namespace Drupal\par_notification;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\message\MessageInterface;
use Drupal\message\MessageTemplateInterface;

/**
 * Manages message .
 */
class ParMessageHandler implements ParMessageHandlerInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a ParDataPermissions instance.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Get the entity type manager.
   *
   * @return EntityTypeManagerInterface
   */
  public function getEntityTypeManager(): EntityTypeManagerInterface {
    return $this->entityTypeManager;
  }

  public function getMessagesWithData() {
    $templates = $this->getEntityTypeManager()->getStorage('message_template')->loadMultiple();

    $entity_type = $this->getEntityTypeManager()->getDefinition('message');
    $bundle_key = $entity_type->getKey('bundle');

    $qry = $this->getEntityTypeManager()->getStorage('message')->getQuery();

    $or = $qry->orConditionGroup();
    foreach ($templates as $template) {
      $field = $this->getPrimaryField($template);
      if ($field) {
        $and = $qry->andConditionGroup();
        $and->condition($bundle_key, $template->id());
        $and->condition($field, NULL, 'IS NOT NULL');
        $or->condition($and);
      }
    }

    $qry->condition($or);

    return $qry->execute();
  }

  /**
   * Get the field that hold the primary data for a given message.
   *
   * @todo Enable this information to be configurable on the entity.
   *
   * @param \Drupal\message\MessageTemplateInterface $template
   *   The message template to lookup the primary field for.
   *
   * @return mixed
   *   The field name.
   *
   */
  private function getPrimaryField(MessageTemplateInterface $template): mixed {
    $message_types = [
      'approved_enforcement' => 'field_enforcement_notice',
      'inspection_plan_expiry_warning' => 'field_inspection_plan',
      'revoke_inspection_plan' => 'field_inspection_plan',
      'new_deviation_response' => 'field_comment',
      'new_deviation_request' => 'field_deviation_request',
      'new_enforcement_notification' => 'field_enforcement_notice',
      'new_enquiry_response' => 'field_comment',
      'new_general_enquiry' => 'field_general_enquiry',
      'new_inspection_feedback_response' => 'field_comment',
      'new_inspection_feedback' => 'field_inspection_feedback',
      'new_inspection_plan' => 'field_inspection_plan',
      'new_partnership_notification' => 'field_partnership',
      'partnership_confirmed_notificati' => 'field_partnership',
      'partnership_approved_notificatio' => 'field_partnership',
      'partnership_deleted_notification' => 'field_partnership',
      'partnership_revocation_notificat' => 'field_partnership',
      'reviewed_deviation_request' => 'field_deviation_request',
      'reviewed_enforcement' => 'field_enforcement_notice',
      'member_list_stale_warning' => 'field_partnership',
      'new_response' => NULL,
      'subscription_verify_notification' => NULL,
    ];

    return $message_types[$template->id()] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getPrimaryData(MessageInterface $message): array {
    $field = $this->getPrimaryField($message->getTemplate());
    if ($field && $message->hasField($field)) {
      $primary_data = $message->get($field)->referencedEntities();

      $primary_data = array_filter($primary_data, function ($data) {
        return $data instanceof EntityInterface;
      });
    }

    // If there is a primary field, but it has no data, throw an exception.
    if ($field && (!$message->hasField($field) || empty($primary_data))) {
      throw new ParNotificationException("Primary data missing.");
    }

    return $primary_data ?? [];
  }

  /**
   * Get the thread group for a given message if it has one.
   *
   * @todo Enable this information to be configurable on the entity.
   *
   * @param \Drupal\message\MessageTemplateInterface $template
   *   The message template to lookup the thread group for.
   *
   * @return mixed
   *   The field name.
   *
   */
  public function getMessageGroup(MessageTemplateInterface $template): mixed {
    $groups = [
      'new_enforcement_notification' => 'enforcement_raise',
      'approved_enforcement' => 'enforcement_raise',
      'reviewed_enforcement' => 'enforcement_raise',
      'inspection_plan_expiry_warning' => 'inspection_plan_expire',
      'revoke_inspection_plan' => 'inspection_plan_expire',
      'new_deviation_request' => 'deviation_new',
      'new_deviation_response' => 'deviation_new',
      'reviewed_deviation_request' => 'deviation_new',
      'new_general_enquiry' => 'enquiry_new',
      'new_enquiry_response' => 'enquiry_new',
      'new_inspection_feedback' => 'inspection_feedback_new',
      'new_inspection_feedback_response' => 'inspection_feedback_new',
      'new_inspection_plan' => 'field_inspection_plan',
      'new_partnership_notification' => 'parntership_application',
      'partnership_confirmed_notificati' => 'parntership_application',
      'partnership_approved_notificatio' => 'parntership_application',
      'partnership_deleted_notification' => 'partnership_removal',
      'partnership_revocation_notificat' => 'partnership_removal',
      'member_list_stale_warning' => 'coordinated_membership',
      'subscription_verify_notification' => NULL,
    ];

    return $groups[$template->id()] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getThreadId(MessageInterface $message): string {
    $group = $this->getMessageGroup($message->getTemplate());
    try {
      $primary_data = $this->getPrimaryData($message);
    }
    catch (ParNotificationException $e) {

    }

    $thread = [
      $group ?? $message->getTemplate()->id(),
      $primary_data?->id() ?? $message->id(),
    ];

    return implode(':', $thread);
  }

}
