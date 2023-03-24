<?php

namespace Drupal\par_notification;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\message\MessageInterface;
use Drupal\message\MessageTemplateInterface;
use Drupal\par_notification\ParRecipient;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;
use Drupal\user\RoleStorageInterface;
use Drupal\user\UserInterface;

/**
 * Provides a link management service for notifications.
 *
 * Automatically handling redirection to the primary action link
 * for any given notification message, including sequential
 * redirection where multiple pages need to be accessed one after
 * the other.
 *
 * @see \Drupal\par_notification\ParSubscriptionManagerInterface
 * @see \Drupal\par_notification\Annotation\ParMessageSubscriber
 * @see plugin_api
 */
class ParSubscriptionManager extends DefaultPluginManager implements ParSubscriptionManagerInterface {

  use LoggerChannelTrait;
  use StringTranslationTrait;

  /**
   * The logger channel to use.
   */
  const PAR_LOGGER_CHANNEL = 'par';

  /**
   * The email validator service.
   *
   * @var EmailValidatorInterface
   */
  protected EmailValidatorInterface $emailValidator;

  /**
   * The account object.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a ParLinkManager instance.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   * @param EmailValidatorInterface $email_validator
   *  The email validator service.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, EmailValidatorInterface $email_validator, AccountInterface $user) {
    parent::__construct(
      'Plugin/ParMessageSubscriber',
      $namespaces,
      $module_handler,
      'Drupal\par_notification\ParMessageSubscriberInterface',
      'Drupal\par_notification\Annotation\ParMessageSubscriber'
    );

    $this->alterInfo('par_notification_message_subscriber_info');
    $this->setCacheBackend($cache_backend, 'par_notification_message_subscriber_info_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());

    $this->emailValidator = $email_validator;
    $this->currentUser = $user;
  }

  /**
   * Get the ParDataHandler service.
   *
   * @return RoleStorageInterface
   *   The role storage service.
   */
  private function getRoleStorage(): RoleStorageInterface {
    return $this->roleStorage ?? \Drupal::entityTypeManager()->getStorage('user_role');
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
   * Get the email validator service.
   *
   * @return EmailValidatorInterface
   *   The email validator.
   */
  public function getEmailValidator(): EmailValidatorInterface {
    return $this->emailValidator;
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);

    // Assign any default properties.
    if (!isset($definition['status'])) {
      $definition['status'] = TRUE;
    }
    if (!isset($definition['message'])) {
      $definition['message'] = [];
    }

    if (!isset($definition['rules'])) {
      $definition['rules'] = [
        'rule-based',
        'user-preference-based',
        'membership-based'
      ];
    }
  }

  /**
   * {@inheritDoc}
   *
   * @param bool $only_active
   *   Whether only active plugins should be returned.
   */
  public function getDefinitions(bool $only_active = TRUE): array {
    $definitions = [];

    foreach (parent::getDefinitions() as $id => $definition) {
      if ($definition['status'] || !$only_active) {
        $definitions[] = $definition;
      }
    }

    return $definitions;
  }

  /**
   * Get the definitions for a given message template.
   */
  public function getMessageDefinitions(MessageTemplateInterface $message_template): array {
    // Retrieve definitions once per notification type.
    $function_id = __FUNCTION__ . ':' . $message_template->id();
    $definitions = &drupal_static($function_id);
    if (isset($definitions)) {
      return $definitions;
    }

    $definitions = [];
    foreach ($this->getDefinitions() as $id => $definition) {
      if (in_array($message_template->id(), $definition['message'])) {
        $definitions[$id] = $definition;
      }
    }

    return $definitions;
  }

  /**
   * {@inheritDoc}
   */
  public function getRecipients(MessageInterface $message): array {
    $email_validator = $this->getEmailValidator();
    $current_user = $this->getCurrentUser();
    $recipients = [];

    /** @var ParMessageSubscriberInterface[] $subscribers */
    $subscribers = $this->getMessageDefinitions($message->getTemplate());
    foreach ($subscribers as $definition) {
      $subscriber = $this->createInstance($definition['id'], $definition);
      try {
        /** @var ParRecipient[] $plugin_recipients */
        $plugin_recipients = $subscriber->getRecipients($message);
      }
      catch (ParNotificationException $e) {
        // Do not bubble up subscriber errors.
        continue;
      }

      // Add the recipients to the return array.
      $recipients = array_merge($recipients, $plugin_recipients);
    }

    // Validate the email addresses.
    $recipients = array_filter($recipients, function ($recipient) use ($email_validator) {
      return $email_validator->isValid($recipient->getEmail());
    });

    // Exclude the current user from receiving notifications of their own actions.
    $recipients = array_filter($recipients, function ($recipient) use ($current_user) {
      return !$current_user->getEmail() ||
        $current_user->getEmail() != $recipient->getEmail();
    });

    // Only allow users (including anonymous) who have the permission to see this message.
    $permission = "receive {$message->getTemplate()->id()} notification";
    $recipients = array_filter($recipients, function ($recipient) use ($permission) {
      return $recipient->getAccount()->hasPermission($permission);
    });

    // Compare the ParRecipient instances using string representation.
    return array_unique($recipients, SORT_STRING);
  }

  public function getRecipientEmails(MessageInterface $message): array {
    $recipients = $this->getRecipients($message);

    return array_values(array_map(function($recipient) {
      return $recipient->getEmail();
    }, $recipients));
  }

  /**
   * {@inheritDoc}
   */
  public function getSubscribedEntities(MessageInterface $message): array {
    $subscribed_entities = [];

    /** @var ParMessageSubscriberInterface[] $subscribers */
    $subscribers = $this->getMessageDefinitions($message->getTemplate());
    foreach ($subscribers as $definition) {
      $subscriber = $this->createInstance($definition['id'], []);
      try {
        $plugin_subscribed_entities = $subscriber->getSubscribedEntities($message);
      }
      catch (ParNotificationException $e) {
        // Do not bubble up subscriber errors.
        continue;
      }

      // Add the subscribed entities to the return array.
      $subscribed_entities = array_merge($subscribed_entities, $plugin_subscribed_entities);
    }

    return $subscribed_entities;
  }

  /**
   * {@inheritDoc}
   */
  public function getSubscribedRoles(MessageTemplateInterface $template): array {
    $role_storage = \Drupal::entityTypeManager()->getStorage('user_role');
    $permission = "receive {$template->id()} notification";

    /** @var Role[] $roles */
    $roles = $role_storage->loadMultiple();

    $roles = array_filter($roles, function($role) use ($permission) {
      return ($role->hasPermission($permission));
    });

    // Throw an error if there are no roles to receive this message.
    if (empty($roles)) {
      throw new ParNotificationException('There are no roles to receive this message.');
    }

    return $roles;
  }

  /**
   * Get all the roles that grant the user the permission to receive this message.
   *
   * This excludes any roles that the user does not have, or that do not grant
   * permission to receive this message type.
   *
   * @param AccountInterface $account
   *   The user account to cross-check with.
   * @param MessageTemplateInterface $message
   *   The message type to get the roles for.
   *
   * @return RoleInterface[]
   *   An array of user roles.
   */
  public function getUserNotificationRoles(AccountInterface $account, MessageTemplateInterface $message): array {
    // Compare all the available roles with the user's roles.
    $roles = $this->getSubscribedRoles($message);
    $user_roles = !empty($account->getRoles()) ?
      $this->getRoleStorage()->loadMultiple($account->getRoles()) :
      NULL;

    // Get the intersection between the user's roles and the notifications roles.
    return array_uintersect($roles, $user_roles, function ($a, $b) {
      return $a->id() <=> $b->id();
    });
  }

  /**
   * Get the roles that apply to the subscribed entities.
   *
   * Filters roles that have the 'bypass par_data membership' permission,
   * these roles don't take membership into consideration and don't apply
   * to subscribed entities e.g. helpdesk users.
   *
   * Always excludes the anonymous role.
   *
   * @param RoleInterface[] $roles
   *   An array of roles to check.
   * @param bool $include
   *   Whether to include only the subscribed entity roles (default)
   *   Or exclude them, and return all other roles.
   *
   * @param RoleInterface[]
   *   The roles that apply to the subscribed entities.
   */
  public function filterSubscribedEntityRoles(array $roles, bool $include = TRUE): ?array {
    $permission = 'bypass par_data membership';
    return array_filter($roles, function($role) use ($permission, $include) {
      return $role->id() !== RoleInterface::ANONYMOUS_ID &&
        ( $include && !$role->hasPermission($permission) ||
          !$include && $role->hasPermission($permission) );
    });
  }

}
