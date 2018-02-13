<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "legal_entity",
 *   title = @Translation("Legal entity form.")
 * )
 */
class ParLegalEntityForm extends ParFormPluginBase {

  /**
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_legal_entity:legal_entity' => [
      'registered_name' => 'registered_name',
      'legal_entity_type' => 'legal_entity_type',
      'registered_number' => 'registered_number',
    ]
  ];

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    if ($cardinality === 1) {
      $form['legal_entity_intro_fieldset'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('What is a legal entity?'),
        'intro' => [
          '#type' => 'markup',
          '#markup' => "<p>" . $this->t("A legal entity is any kind of individual or organisation that has legal standing. This can include a limited company or partnership, as well as other types of organisations such as trusts and charities.") . "</p>",
        ]
      ];
    }

    $form['legal_entity'] = [
      '#type' => 'fieldset',
      '#title' => $this->formatPlural($this->getCardinality(), 'Legal Entity', 'Legal Entity @index', ['@index' => $cardinality]),
    ];

    $form['registered_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter name of the legal entity'),
      '#default_value' => $this->getDefaultValuesByKey('registered_name', $cardinality),
    ];

    $form['legal_entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Select type of Legal Entity'),
      '#default_value' => $this->getDefaultValuesByKey('legal_entity_type', $cardinality, 'public_limited_company'),
      '#options' => $legal_entity_bundle->getAllowedValues('legal_entity_type'),
    ];

    $form['registered_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Provide the registration number'),
      '#default_value' => $this->getDefaultValuesByKey('registered_number', $cardinality),
      '#states' => [
        'visible' => [
          'select[name="' . $this->getElementName('legal_entity_type', $cardinality) . '"]' => [
            ['value' => 'limited_company'],
            ['value' => 'public_limited_company'],
            ['value' => 'limited_liability_partnership'],
            ['value' => 'registered_charity'],
            ['value' => 'partnership'],
            ['value' => 'limited_partnership'],
            ['value' => 'other'],
          ],
        ],
      ],
    ];

    return $form;
  }
}
