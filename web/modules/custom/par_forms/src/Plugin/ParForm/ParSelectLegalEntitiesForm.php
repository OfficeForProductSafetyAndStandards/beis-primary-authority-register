<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Select Legal Entities form.
 *
 * @ParForm(
 *   id = "select_legal_entities",
 *   title = @Translation("Select legal entities form.")
 * )
 */
class ParSelectLegalEntitiesForm extends ParFormPluginBase {

  use ParDisplayTrait;

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $par_data_partnership = $this->getflowDataHandler()
      ->getParameter('par_data_partnership');
    $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);

    // Get legal entities on the PAR Organisation.
    $this->getFlowDataHandler()
      ->setTempDataValue('organisation_legal_entities', $par_data_organisation->getLegalEntity());

    $this->getFlowDataHandler()
      ->setTempDataValue('partnership_legal_entities', $par_data_partnership->retrieveEntityIds('field_legal_entity'));

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = []) {
    // Retrieve legal entities on the partnership.
    $organisation_legal_entities = $this->getFlowDataHandler()
      ->getTempDataValue('organisation_legal_entities');

    // Retrieve legal entities on the partnership.
    $partnership_legal_entities = $this->getFlowDataHandler()
      ->getTempDataValue('partnership_legal_entities');

    // Get view builder dependency to render legal entities.
    $legal_entities_view_builder = $this->getParDataManager()
      ->getViewBuilder('par_data_legal_entity');

    // Build options for legal entities forms.
    foreach ($organisation_legal_entities as $id => $legal_entity) {
      $legal_entity_view = $legal_entities_view_builder->view($legal_entity, 'summary');
      $legal_entities_options[$legal_entity->id()] = $this->renderMarkupField($legal_entity_view)['#markup'];
    }

    // Intro text.
    $form['legal_entity_intro'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('What is a legal entity?'),
      'text' => [
        '#type' => 'markup',
        '#markup' => $this->t('A legal entity is any kind of individual or organisation that has legal standing. This can include a
limited company or partnership, as well as other types of organisations such as trusts and charities.'),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ],
    ];

    if (!empty($legal_entities_options)) {
      // Checkboxes for legal entities.
      $form['field_legal_entity'] = [
        '#type' => 'checkboxes',
        '#attributes' => ['class' => ['form-group']],
        '#title' => t('Choose which legal entities this partnership relates to'),
        '#options' => $legal_entities_options,
        // Automatically check all legal entities if no form data is found.
        '#default_value' => $this->getFlowDataHandler()
          ->getDefaultValues("field_legal_entity", $partnership_legal_entities),
      ];
    }
    else {
      $form['organisation_no_legal_entities_message'] = [
        '#type' => 'markup',
        '#markup' => $this->t("No legal entities found on the PAR Organisation"),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ];
    }

    // A note to the user that they can add a new legal entity on the next step.
    $form['legal_entity_add_more_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Additional legal entities'),
      'text' => [
        '#type' => 'markup',
        '#markup' => $this->t("In the next step you can add one or more legal entities to this partnership."),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ],
    ];

    return $form;

  }

}
