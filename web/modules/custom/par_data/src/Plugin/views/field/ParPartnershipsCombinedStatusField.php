<?php

/**
 * @file
 * Definition of Drupal\par_data\Plugin\views\field\ParPartnershipsCombinedStatusField
 */

namespace Drupal\par_data\Plugin\views\field;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

use Drupal\Core\Form\FormStateInterface;

/**
 * Field handler to get the PAR Partnership Combined Status Fields.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_partnerships_combined_status_field")
 */
class ParPartnershipsCombinedStatusField extends FieldPluginBase {

  /*
   * @{inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['status_fields'] = ['default' => []];
    $options['status_on_label'] = ['default' => 'Confirmed'];
    $options['status_off_label'] = ['default' => 'Awaiting Review'];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    // @todo figure out how to get the label from the entity configuration.
    $form['status_fields'] = [
      '#type' => 'checkboxes',
      '#title' => 'Status fields to include',
      '#description' => 'Select multiple PAR Partnership status fields',
      '#options' => [
        'partnership_info_agreed_authority' => 'PA Partnership Info Agreed',
        'partnership_info_agreed_business' => 'Business Partnership Details Confirmed',
        'terms_authority_agreed' => 'PA Terms and Conditions',
        'terms_organisation_agreed' => 'Business/Organisation Terms Agreed'
      ],
      '#default_value' => $this->options['status_fields']  ?: []
    ];

    $form['status_on_label'] = [
      '#title' => 'On Label',
      '#description' => 'Text to show when all fields return true',
      '#type' => 'textfield',
      '#default_value' => $this->options['status_on_label'] ?: ''
    ];

    $form['status_off_label'] = [
      '#title' => 'Off Label',
      '#description' => 'Text to show when not all fields are true',
      '#type' => 'textfield',
      '#default_value' => $this->options['status_off_label'] ?: ''
    ];
  }

  /**
   * @{inheritdoc}
   *
   * @param ResultRow $values
   *
   * @return string $documentation_completion
   */
  public function render(ResultRow $values) {
    $entity = $values->_entity;

    if ($entity instanceof ParDataPartnership) {

      // Filter out combined fields settings.
      $combined_fields = array_filter($this->options['status_fields']);

      foreach ($combined_fields as $field_key => $field_val) {
        $status_fields[] = $entity->get($field_key)->getString();
      }

      // Check if all true.
      $status = (count(array_keys($status_fields, 1)) == count($status_fields)) ?
        $this->options['status_on_label'] :
        $this->options['status_off_label'];

      return t($status);

    }
  }
}
