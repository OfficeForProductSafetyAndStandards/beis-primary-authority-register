<?php

namespace Drupal\par_data\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\views\ViewExecutable;

use Drupal\user\Entity\User;

/**
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("par_member")
 */
class ParMember extends FilterPluginBase {

  /**
   * Getter for the PAR Data Manager serice.
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * @param \Drupal\views\ViewExecutable $view
   * @param \Drupal\views\Plugin\views\display\DisplayPluginBase $display
   * @param array|null $options
   */
  #[\Override]
  public function init(ViewExecutable $view, DisplayPluginBase $display, ?array &$options = NULL) {
    parent::init($view, $display, $options);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  protected function valueForm(&$form, FormStateInterface $form_state) {
    // Whether to show only direct memberships.
    $form['value'] = [
      '#type' => 'checkbox',
      '#title' => t('Direct membership only (memberships will only be listed if they are directly related to an authority or organisation)'),
      '#default_value' => $this->value,
      '#return_value' => "direct",
    ];
  }

  /**
   * {@inheritdoc)
   */
  #[\Override]
  public function query() {
    // This filter does not apply if not on a PAR entity.
    if (!$this->getParDataManager()->getParEntityType($this->getEntityType())) {
      return;
    }

    // Get current user and check permissions.
    $account = User::load(\Drupal::currentUser()->id());
    if ($account->hasPermission('bypass par_data membership')) {
      return;
    }

    // Find memberships.
    $membership_filter = [];
    $memberships = $this->getParDataManager()->hasMembershipsByType($account, $this->getEntityType(), (bool) ($this->value === 'direct'));
    foreach ($memberships as $membership) {
      $membership_filter[] = $membership->id();
    }

    // Add 0 to prevent an invalid IN query.
    array_push($membership_filter, 0);

    $par_entity_type = $this->getParDataManager()->getParEntityType($this->getEntityType());

    // The normal use of ensureMyTable() here breaks Views.
    // So instead we trick the filter into using the alias of the base table.
    // @see https://www.drupal.org/node/271833.
    // If a relationship is set, we must use the alias it provides.
    if (!empty($this->relationship)) {
      $this->tableAlias = $this->relationship;
    }
    // If no relationship, then use the alias of the base table.
    else {
      $this->tableAlias = $this->query->ensureTable($this->view->storage->get('base_table'));
    }

    // Get field to query e.g. "par_partnerships_field_data.id".
    $id = $par_entity_type->getKeys()['id'];
    $revision_table = "{$this->tableAlias}.{$id}";

    // Where filter on partnership id to those the user is allowed to update.
    $this->query->addWhere(0, $revision_table, $membership_filter, 'IN');
  }

}
