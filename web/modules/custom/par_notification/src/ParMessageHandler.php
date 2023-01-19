<?php

namespace Drupal\par_notification;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\message\MessageInterface;
use Drupal\message\MessageTemplateInterface;
use Drupal\message_notify\MessageNotifier;

/**
 * Manages message creation and handles the extraction of values from each message.
 */
class ParMessageHandler implements ParMessageHandlerInterface {

  use StringTranslationTrait;
  use LoggerChannelTrait;

  /**
   * The notification plugin that will deliver these notification messages.
   */
  const DELIVERY_METHOD = 'plain_email';

  /**
   * The entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The subscription manager.
   *
   * @var ParSubscriptionManagerInterface
   */
  protected ParSubscriptionManagerInterface $subscriptionManager;

  /**
   * The notification link manager.
   *
   * @var ParLinkManagerInterface
   */
  protected ParLinkManagerInterface $linkManager;

  /**
   * The message notifier.
   *
   * @var MessageNotifier
   */
  protected MessageNotifier $messageNotifier;

  /**
   * The current user.
   *
   * @var AccountInterface
   */
  protected AccountInterface $currentUser;

  /**
   * Constructs a ParMessageHandler instance.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   *  The entity type manager.
   * @param ParSubscriptionManagerInterface $subscription_manager
   *  The subscription manager.
   * @param ParLinkManagerInterface $link_manager
   *  The notification link manager.
   * @param MessageNotifier $message_notifier
   *  The message notifier.
   * @param AccountInterface $user
   *  The current user.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ParSubscriptionManagerInterface $subscription_manager, ParLinkManagerInterface $link_manager, MessageNotifier $message_notifier, AccountInterface $user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->subscriptionManager = $subscription_manager;
    $this->linkManager = $link_manager;
    $this->messageNotifier = $message_notifier;
    $this->currentUser = $user;
  }

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel(): string {
    return 'par';
  }

  /**
   * Get the entity type manager.
   *
   * @return EntityTypeManagerInterface
   */
  public function getEntityTypeManager(): EntityTypeManagerInterface {
    return $this->entityTypeManager;
  }

  /**
   * Get the par subscription manager.
   *
   * @return ParSubscriptionManagerInterface
   */
  public function getSubscriptionManager(): ParSubscriptionManagerInterface {
    return $this->subscriptionManager;
  }

  /**
   * Get the par notification link manager.
   *
   * @return ParLinkManagerInterface
   */
  public function getLinkManager(): ParLinkManagerInterface {
    return $this->linkManager;
  }

  /**
   * Get the message notifier.
   *
   * @return MessageNotifier
   */
  public function getMessageNotifier(): MessageNotifier {
    return $this->messageNotifier;
  }

  /**
   * Get the current user.
   *
   * @return AccountInterface
   *   The current user.
   */
  public function getCurrentUser(): AccountInterface {
    return $this->currentUser;
  }

  /**
   * Get message template storage.
   *
   * @return EntityStorageInterface
   *   The entity storage for message template (bundle) entities.
   */
  public function getMessageTemplateStorage(): EntityStorageInterface {
    return $this->getEntityTypeManager()->getStorage('message_template');
  }

  /**
   * Get message storage.
   *
   * @return EntityStorageInterface
   *   The entity storage for messages.
   */
  public function getMessageStorage(): EntityStorageInterface {
    return $this->getEntityTypeManager()->getStorage('message');
  }

  /**
   * {@inheritdoc}
   */
  public function createMessage(string $template_id) {
    // Check that a template could be found.
    /** @var MessageTemplateInterface $message_template */
    $message_template = $this->getMessageTemplateStorage()->load($template_id);

    // Create one message per user.
    if (!$message_template?->id()) {
      throw new ParNotificationException('No template could be found.');
    }

    /** @var MessageInterface $message */
    $message = $this->getMessageStorage()->create([
      'template' => $message_template?->id(),
    ]);

    // Add the standard personalisation arguments.
    $arguments = [
      '@first_name' => [
        'callback' => [self::class, 'personalise'],
        'pass message' => TRUE,
      ],
    ];
    $message->setArguments(array_merge($message->getArguments(), $arguments));

    // The owner is the user who created the message.
    // Even though they may not be the one who can see it.
    if ($this->getCurrentUser()->isAuthenticated()) {
      $message->setOwnerId($this->getCurrentUser()->id());
    }

    return $message;
  }

  /**
   * {@inheritdoc}
   */
  public function sendMessage(MessageInterface $message) {
    // Get all the primary tasks for this notification.
    $tasks = $this->getLinkManager()->retrieveTasks($message->getTemplate());

    // If any of the tasks have been completed then throw a message error.
    foreach ($tasks as $id => $task) {
      if ($task->isComplete($message)) {
        return;
      }
    }

    if ($message->hasField('field_to')) {
      // Send the message to all recipients listed in 'field_to'.
      foreach ($message->get('field_to')->getValue() as $delta => $value) {
        $email = $value['value'];

        $this->getMessageNotifier()->send(
          $message,
          ['mail' => $email],
          static::DELIVERY_METHOD
        );
      }
    }
    else {
      $this->getLogger($this->getLoggerChannel())->warning('Could not send the \'%message_template\' message.', ['%message_template' => $message->getTemplate()->label()]);
    }
  }

  /**
   * Message personalisation callback.
   */
  static function personalise(MessageInterface $message) {
    return 'Primary Authority User';
  }

  /**
   * Get all the messages that have data attached to them.
   *
   * This function is temporary and only needed for install hooks.
   *
   * @return MessageInterface[]
   *   An array of messages.
   */
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
  public function getPrimaryField(MessageTemplateInterface $template): mixed {
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
  private function getMessageGroup(MessageTemplateInterface $template): mixed {
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
