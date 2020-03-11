<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Render\Element;
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
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('is_local_authority', FALSE),
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
}
