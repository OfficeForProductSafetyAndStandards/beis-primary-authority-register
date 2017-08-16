<?php

namespace Drupal\par_data\Plugin\views\filter;

use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\views\ViewExecutable;

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

    // @todo use method to get partnerships the user has "update" access to.
    $partnerships_filter = [1,2,3,4,5,6,7,8,9,10];

    // Where filter on partnership id to those the user is allowed to update.
    $this->query->addWhere(0, 'par_partnerships_field_revision.id', $partnerships_filter, 'in');

  }

}
