<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * Legal Entity form plugin.
 *
 * @ParForm(
 *   id = "legal_entity",
 *   title = @Translation("Legal entity form.")
 * )
 */
class ParLegalEntityForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['registered_name', 'par_data_legal_entity', 'registered_name', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the name of this legal entity.'
    ]],
    ['legal_entity_type', 'par_data_legal_entity', 'legal_entity_type', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must choose which type of legal entity this is.'
    ]],
    ['registered_number', 'par_data_legal_entity', 'registered_number', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the registered number for this legal entity.'
    ]],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($par_data_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity')) {
      $this->getFlowDataHandler()->setFormPermValue("registered_name", $par_data_legal_entity->get('registered_name')->getString());
      $this->getFlowDataHandler()->setFormPermValue("legal_entity_type", $par_data_legal_entity->get('legal_entity_type')->getString());
      $this->getFlowDataHandler()->setFormPermValue("registered_number", $par_data_legal_entity->get('registered_number')->getString());
    }

    parent::loadData();
  }

  /**
   * @defaults
   */
  protected $formDefaults = [
    'legal_entity_type' => 'none',
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

    $legal_entity_label = $this->getCardinality() !== 1 ?
      $this->formatPlural($cardinality, 'Legal Entity @index', 'Legal Entity @index (Optional)', ['@index' => $cardinality]) :
      $this->t('Legal Entity');

    $form['legal_entity'] = [
      '#type' => 'fieldset',
      '#title' => $legal_entity_label,
    ];

    $form['registered_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter name of the legal entity'),
      '#default_value' => $this->getDefaultValuesByKey('registered_name', $cardinality),
    ];

    $form['legal_entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Select type of Legal Entity'),
      '#default_value' => $this->getDefaultValuesByKey('legal_entity_type', $cardinality, $this->getFormDefaultByKey('legal_entity_type')),
      '#options' => ['' => ''] + $legal_entity_bundle->getAllowedValues('legal_entity_type'),
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
