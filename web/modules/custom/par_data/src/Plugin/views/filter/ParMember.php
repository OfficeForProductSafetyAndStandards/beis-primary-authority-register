<?php

namespace Drupal\par_data\Plugin\views\filter;

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

  protected $par_data_manager;

  /**
   * @param \Drupal\views\ViewExecutable $view
   * @param \Drupal\views\Plugin\views\display\DisplayPluginBase $display
   * @param array|NULL $options
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);

    $this->par_data_manager = \Drupal::service('par_data.manager');
  }

  /**
   * {@inheritdoc)
   */
  public function query() {

    // Get current user ID.
    $account = User::load(\Drupal::currentUser()->id());

    // Find memberships.
    $partnerships_filter = $this->par_data_manager->hasMemberships($account);

    // Add 0 to prevent an invalid IN query.
    $partnerships_filter[] = 0;

    // @todo refactor to allow other entity types.
    $par_data_partnership_type = $this->par_data_manager->getParEntityType('par_data_partnership');

    // Get partnerships field e.g. "par_partnerships_field_data.id".
    $revision_table = $par_data_partnership_type->getDataTable() . '.id';

    // Where filter on partnership id to those the user is allowed to update.
    $this->query->addWhere(0, $revision_table, $partnerships_filter, 'in');

  }

}
