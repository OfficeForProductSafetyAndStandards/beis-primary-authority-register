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

    // Get data table.
    $data_table = $this->par_data_manager->getParEntityType($this->getEntityType())->get('data_table');

    $field = "{$data_table}.{$this->realField}";

    $this->query->addWhere(0, $field, $this->value, 'in');
  }

}
