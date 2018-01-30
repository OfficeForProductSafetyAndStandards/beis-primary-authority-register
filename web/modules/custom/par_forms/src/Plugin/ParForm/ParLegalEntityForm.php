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
   * Load the data for this form.
   */
  public function loadData() {
    if ($par_data_legal_entity = $this->getflowDataHandler()->getParameter('par_data_legal_entity')) {
      $this->getFlowDataHandler()->setFormPermValue("legal_entity_registered_name", $par_data_legal_entity->get('registered_name')->getString());
      $this->getFlowDataHandler()->setFormPermValue("legal_entity_registered_number", $par_data_legal_entity->get('registered_number')->getString());
      $this->getFlowDataHandler()->setFormPermValue("legal_entity_legal_entity_type", $par_data_legal_entity->get('legal_entity_type')->getString());
      $this->getFlowDataHandler()->setFormPermValue('legal_entity_id', $par_data_legal_entity->id());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = []) {
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    $form['legal_entity_intro_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('What is a legal entity?'),
    ];

    $form['legal_entity_intro_fieldset']['intro'] = [
      '#type' => 'markup',
      '#markup' => "<p>" . $this->t("A legal entity is any kind of individual or organisation that has legal standing. This can include a limited company or partnership, as well as other types of organisations such as trusts and charities.") . "</p>",
    ];

    $form['registered_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter name of the legal entity'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_registered_name"),
    ];

    $form['legal_entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Select type of Legal Entity'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_legal_entity_type"),
      '#options' => $legal_entity_bundle->getAllowedValues('legal_entity_type'),
    ];

    $form['registered_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Provide the registration number'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_registered_number"),
      '#states' => [
        'visible' => [
          'select[name="legal_entity_type"]' => [
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
