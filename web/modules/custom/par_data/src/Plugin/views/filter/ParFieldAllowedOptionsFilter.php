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
 * @ViewsFilter("par_data_field_allowed_options_filter")
 */
class ParFieldAllowedOptionsFilter extends FilterPluginBase {

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
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $entity_bundle = $this->par_data_manager->getParBundleEntity($this->getEntityType());
    $allowed_values = $entity_bundle->getAllowedValues($this->realField);

    $form['value'] = [
      '#type' => 'select',
      '#options' => $allowed_values,
      '#default_value' => $this->value,
    ];
  }

  /**
   * {@inheritdoc)
   */
  public function query() {
    // This filter does not apply if not on a PAR entity.
    if (!$this->par_data_manager->getParEntityType($this->getEntityType())) {
      return;
    }

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

    // Get field to query e.g. "par_partnerships_field_data.partnership_status".
    $field = "{$this->tableAlias}.{$this->realField}";

    $this->query->addWhere(0, $field, $this->value, 'in');
  }

}
