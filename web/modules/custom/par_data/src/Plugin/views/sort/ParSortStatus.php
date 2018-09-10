<?php

namespace Drupal\par_data\Plugin\views\sort;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataType;
use Drupal\par_data\Entity\ParDataTypeInterface;
use Drupal\views\FieldAPIHandlerTrait;
use Drupal\views\Plugin\views\sort\SortPluginBase;

/**
 * Sort handler for fields with allowed_values.
 *
 * @ingroup views_sort_handlers
 *
 * @ViewsSort("par_sort_allowed_statuses")
 */
class ParSortStatus extends SortPluginBase {

  use FieldAPIHandlerTrait;

  /**
   * Getter for the PAR Data Manager serice.
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * Options definition.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['allowed_values'] = array('default' => 0);
    $options['null_heavy'] = array('default' => 0);
    return $options;
  }

  /**
   * Options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['allowed_values'] = array(
      '#type' => 'radios',
      '#title' => t('Sort by status'),
      '#options' => array(t('No'), t('Yes')),
      '#default_value' => $this->options['allowed_values'],
    );
    $form['null_heavy'] = array(
      '#type' => 'radios',
      '#title' => t('Treat null values as heavier than the allowed values'),
      '#options' => array(t('No'), t('Yes')),
      '#default_value' => $this->options['null_heavy'],
    );
  }

  /**
   * Called to add the sort to a query.
   *
   * Sort by index of allowed values using sql FIELD function.
   *
   * @see http://dev.mysql.com/doc/refman/5.5/en/string-functions.html#function_field
   */
  public function query() {
    $this->ensureMyTable();
    // Skip if disabled.
    if (!$this->options['allowed_values']) {
      return;
    }

    $par_entity_type = $this->getParDataManager()->getParEntityType($this->getEntityType());
    $entity_bundle = $par_entity_type ? $this->getParDataManager()->getParBundleEntity($par_entity_type->id()) : NULL;
    if ($entity_bundle instanceof ParDataTypeInterface) {
      $status_values = $entity_bundle->getAllowedValues($entity_bundle->getConfigurationElementByType('entity', 'status_field'));
    }
    else {
      $status_values = [];
    }

    $allowed_values = array_keys($status_values);
    $connection = Database::getConnection();

    $formula = '';
    // Reverse the values returned by the FIELD function and the allowed values
    // so '0' is heavier than the rest.
    if ($this->options['null_heavy']) {
      $allowed_values = array_reverse($allowed_values);
      $formula .= '-1 * ';
    }

    if (!empty($allowed_values)) {
      $table = $this->tableAlias . '.' . $this->field;
      $formula .= "CASE ";
      foreach ($allowed_values as $index => $key) {
        $formula .= "WHEN $table='$key' THEN $index";
      }
      $formula .= "ELSE 999999999 END";

      $this->query->addOrderBy(NULL, $formula, $this->options['order'], $this->tableAlias . '_' . $this->field . '_par_status');
    }
  }

}
