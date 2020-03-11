<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Render\Element;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "partnership_regulatory_functions",
 *   title = @Translation("Partnership Regulatory Functions form.")
 * )
 */
class ParPartnershipRegulatoryFunctionsForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    // Decide which entity to use.
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      $existing_selection = $par_data_partnership->getRegulatoryFunction();
      $this->getFlowDataHandler()->setFormPermValue("regulatory_functions", array_keys($this->getParDataManager()->getEntitiesAsOptions($existing_selection)));

      // Get the available options.
      if ($authority = $par_data_partnership->getAuthority(TRUE)) {
        $available_regulatory_functions = $authority->getRegulatoryFunction();
        $this->getFlowDataHandler()->setFormPermValue("regulatory_function_options", $this->getParDataManager()->getEntitiesAsOptions($available_regulatory_functions));

        $default = empty(array_diff(array_keys($available_regulatory_functions), array_keys($existing_selection)));
        $this->getFlowDataHandler()->setFormPermValue("default", $default);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $renderer = \Drupal::service('renderer');
    $default_label = [
      [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Normal or Sequenced'),
      ],
      [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Either for normal partnerships, where the organisation only has one partnership, or for sequenced partnerships, where a business wishes to enter into a partnership with more than one local authority and the regulatory functions of those local authorities do not overlap.'),
        '#attributes' => ['class' => 'form-hint'],
      ],
      [
        '#theme' => 'item_list',
        '#list_type' => 'ul',
        '#title' => 'The following regulatory functions will be added',
        '#items' => $this->getFlowDataHandler()->getFormPermValue('regulatory_function_options'),
        '#attributes' => ['class' => ['list', 'list-bullet']],
        '#wrapper_attributes' => ['class' => 'form-group'],
      ]
    ];
    $bespoke_label = [
      [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Bespoke'),
      ],
      [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Bespoke partnerships should only be selected when a business wishes to enter into a partnership with more than one local authority and the regulatory functions of those local authorities overlap.'),
        '#attributes' => ['class' => 'form-hint'],
      ],
    ];

    // Default partnerships are those that cover all the regulatory functions
    // that the authority can offer.
    $default = $this->getFlowDataHandler()->getDefaultValues('default', TRUE);
    $form['partnership_cover'] = [
      '#type' => 'radios',
      '#title' => 'Is this a sequenced or bespoke partnership?',
      '#options' => [
        'default' => $renderer->render($default_label),
        'bespoke' => $renderer->render($bespoke_label),
      ],
      '#options_descriptions' => array(
        'default' => 'Either for normal partnerships, where the organisation only has one partnership, or for sequenced partnerships, where a business wishes to enter into a partnership with more than one local authority and the regulatory functions of those local authorities do not overlap.',
        'bespoke' => 'Bespoke partnerships should only be selected when a business wishes to enter into a partnership with more than one local authority and the regulatory functions of those local authorities overlap.',
      ),
      '#default_value' => $default ? 'default' : 'bespoke',
    ];

    // Set the default regulatory functions to be applied to all default partnerships.
    $values = [];
    foreach ($this->getFlowDataHandler()->getFormPermValue('regulatory_function_options') as $key => $option) {
      $values[$key] = (string) $key;
    }
    $form['default_regulatory_functions'] = [
      '#type' => 'value',
      '#value' => $values,
    ];

    $form['regulatory_functions'] = [
      '#type' => 'checkboxes',
      '#title' => '',
      '#options' => $this->getFlowDataHandler()->getFormPermValue('regulatory_function_options'),
      '#default_value' => $this->getDefaultValuesByKey('regulatory_functions', $cardinality, []),
      '#attributes' => ['class' => ['form-group']],
      '#states' => [
        'visible' => [
          'input[name="partnership_cover"]' => ['value' => 'bespoke'],
        ],
        'disabled' => [
          'input[name="partnership_cover"]' => ['value' => 'default'],
        ],
      ],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $partnership_cover_key = $this->getElementKey('partnership_cover');
    $regulatory_functions_key = $this->getElementKey('regulatory_functions');
    $default_regulatory_functions_key = $this->getElementKey('default_regulatory_functions');

    // For default partnerships the regulatory functions need to be set as the default options.
    if ($form_state->getValue($partnership_cover_key) === 'default') {
      $form_state->setValue($regulatory_functions_key, $form_state->getValue($default_regulatory_functions_key));
    }

    return parent::validate($form, $form_state, $cardinality, $action);
  }
}
