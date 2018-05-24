<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "select_enforced_legal_entity",
 *   title = @Translation("Legal entity selection for enforcement form.")
 * )
 */
class ParSelectEnforcedLegalEntityForm extends ParFormPluginBase {

  const ADD_NEW = 'add_new';

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $select_legal_entities = [];

    // Get all the legal entities for the given organisation.
    if ($par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation')) {
      $legal_entities = $par_data_organisation->getLegalEntity();
      $select_legal_entities = $this->getParDataManager()->getEntitiesAsOptions($legal_entities, $select_legal_entities);
    }

    $this->getFlowDataHandler()->setFormPermValue('select_legal_entities', $select_legal_entities);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Get all the allowed authorities.
    $select_legal_entities = $this->getFlowDataHandler()->getFormPermValue('select_legal_entities');

    $form['alternative_legal_entity'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the name of the legal entity'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("alternative_legal_entity"),
      '#weight' => 101,
    ];

    // If the partnership is direct or there is only one member go to the next step.
    if (count($select_legal_entities) >= 1) {
      // Add the ability to add a non-associated legal entity name.
      $select_legal_entities[self::ADD_NEW] = 'Add a legal entity';

      $form['legal_entities_select'] = [
        '#type' => 'radios',
        '#title' => $this->t('Select a legal entity'),
        '#options' => $select_legal_entities,
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('legal_entities_select', key($select_legal_entities)),
        '#weight' => 100,
        '#required' => TRUE,
        '#prefix' => '<div>',
        '#suffix' => '</div>',
      ];

      // Make sure the alternative legal entity field is selectively available.
      $form['alternative_legal_entity']['#states'] = [
        'visible' => [
          ':input[name="legal_entities_select"]' => ['value' => 'add_new'],
        ],
      ];
    }
    else {
      $form['legal_entities_select'] = [
        '#type' => 'hidden',
        '#value' => self::ADD_NEW,
      ];
    }

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validateForm(&$form_state, $cardinality = 1) {
    $legal_entity = $this->getElementKey('legal_entities_select');
    $alternative_legal_entity = $this->getElementKey('alternative_legal_entity');
    if (empty($form_state->getValue($legal_entity)) && empty($form_state->getValue($alternative_legal_entity))) {
      $form_state->setErrorByName($legal_entity, $this->t('<a href="#edit-legal_entities_select">You must choose a legal entity.</a>'));
    }

    parent::validate($form_state, $cardinality);
  }
}
