<?php

namespace Drupal\par_notification\Plugin\views\filter;

use Drupal\Core\Cache\UncacheableDependencyTrait;
use Drupal\user\Entity\User;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Message access views filter.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("view_messages")
 */
class ViewMessages extends FilterPluginBase {

  use UncacheableDependencyTrait;

  /**
 *
 */
  #[\Override]
  public function query() {
    $entity_type_manager = \Drupal::entityTypeManager();
    /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager */
    $entity_field_manager = \Drupal::service('entity_field.manager');
    /** @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage $message_storage */
    $message_storage = \Drupal::entityTypeManager()->getStorage('message');

    // Ensure the base table.
    $this->ensureMyTable();

    // Get the data table alias.
    $data_table = \Drupal::entityTypeManager()->getStorage('message')->getDataTable();
    $this->query->ensureTable($data_table, $this->relationship);
    $alias = $this->query->ensureTable($data_table, $this->relationship);

    // Get the dedicated field table aliases.
    $message_tables = $message_storage->getTableMapping();
    $base_fields = $entity_field_manager->getFieldDefinitions('message', 'message');

    $field_aliases = [];
    foreach ($base_fields as $field_name => $field_definition) {
      if ($message_tables->requiresDedicatedTableStorage($field_definition)) {
        $table_name = $message_tables->getDedicatedDataTableName($field_definition);
        $field_column_names = $message_tables->getColumnNames($field_name);
        $main_property = $field_definition->getFieldStorageDefinition()->getMainPropertyName();
        $field_alias = $field_column_names[$main_property];
        $table_alias = $this->query->ensureTable($table_name, $this->relationship);
        $field_aliases[$field_name] = "{$table_alias}.{$field_alias}";
      }
    }

    foreach ($message_tables as $message_table) {
      $this->query->ensureTable($message_table, $this->relationship);
    }

    // This can only work if we're authenticated in.
    if (!\Drupal::currentUser()->isAuthenticated()) {
      return;
    }
    $account = \Drupal::currentUser();

    $message_field_storage_definitions = $entity_field_manager->getFieldStorageDefinitions('message');
    $par_data_manager = \Drupal::service('par_data.manager');
    $par_subscription_manager = \Drupal::service('plugin.manager.par_subscription_manager');

    // Get the message templates the user has permission to view.
    $message_templates = $entity_type_manager
      ->getStorage('message_template')
      ->getQuery()->accessCheck()->execute();
    $message_templates = array_filter($message_templates, fn($template) => $account->hasPermission("receive {$template} notification"));

    // Filter out the message templates that don't require subscription.
    $subscription_templates = array_filter($message_templates,
      function ($message_template) use ($account, $par_subscription_manager, $entity_type_manager) {
        // Load the full template entity.
        $template = $entity_type_manager
          ->getStorage('message_template')
          ->load($message_template);
        // Get the roles which subscribe this user to the message.
        $user_roles = $par_subscription_manager
          ->getUserNotificationRoles($account, $template);
        // Filter out all the roles which require a subscription.
        $subscription_roles = $par_subscription_manager
          ->filterSubscribedEntityRoles($user_roles);
        // Include the template if there are any roles which don't require subscription.
        return !empty($subscription_roles);
      });

    // Get the institutions the user is a member of.
    if (!empty($subscription_templates)) {
      $user = User::load($account->id());
      $user_authorities = $par_data_manager
        ->hasMembershipsByType($user, 'par_data_authority');
      $user_organisations = $par_data_manager
        ->hasMembershipsByType($user, 'par_data_organisation');
    }

    // Get the bundle key for the message query.
    $entity_type = $entity_type_manager->getDefinition('message');
    $bundle_key = $entity_type->getKey('bundle');

    // CONDITION 1: User can't see any messages if they have no permissions.
    $permission_condition = ($this->view->query->getConnection()->condition('AND'));
    if (empty($message_templates)) {
      $permission_condition->isNull("{$alias}.{$bundle_key}");
      // Add the permissions conditions.
      $this->query->addWhere($this->options['group'], $permission_condition);
      return;
    }

    // CONDITION 2: User has permission to view the message template.
    $permission_condition->condition("{$alias}.{$bundle_key}", $message_templates, 'IN');
    // Add the permissions conditions.
    $this->query->addWhere($this->options['group'], $permission_condition);

    // CONDITION 3: User must be a member of the subscribed entities.
    if (!empty($subscription_templates)) {
      $membership_condition = $this->view->query->getConnection()->condition('OR');

      if (!empty($user_authorities)) {
        $user_authority_ids = array_values(array_map(fn($authorities) => (int) $authorities->id(), $user_authorities));
        $membership_condition->condition($field_aliases['field_target_authority'], $user_authority_ids, 'IN');
      }
      if (!empty($user_organisations)) {
        $user_organisation_ids = array_values(array_map(fn($organisations) => (int) $organisations->id(), $user_organisations));
        $membership_condition->condition($field_aliases['field_target_organisation'], $user_organisation_ids, 'IN');
      }

      // Allow the membership condition to pass if the message doesn't require subscription.
      $membership_condition->condition("{$alias}.{$bundle_key}", $subscription_templates, 'NOT IN');

      // Add the membership condition.
      $this->query->addWhere($this->options['group'], $membership_condition);
    }
  }

}
