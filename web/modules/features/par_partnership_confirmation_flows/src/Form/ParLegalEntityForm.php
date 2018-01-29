<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParPartnershipFlowsTrait;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParLegalEntityForm extends ParBaseForm {

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Confirm the legal entity';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_confirmation_legal_entity';
  }

  /**
   * {@inheritdoc}
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
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $par_data_partnership ? $par_data_partnership->getOrganisation(TRUE) : NULL;
    $par_data_legal_entity = $par_data_organisation ? $par_data_organisation->getLegalEntity(TRUE) : NULL;

    // For the apply journey we will always edit the first value.
    $this->getFlowDataHandler()->setParameter('par_data_legal_entity', $par_data_legal_entity);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity');

    // Nullify registration number
    $registered_number_types = [
      'limited_company',
      'public_limited_company',
      'limited_liability_partnership',
      'registered_charity',
      'partnership',
      'limited_partnership',
      'other',
    ];

    // Nullify registered number if not one of the types specified.
    if ($legal_entity && !in_array($this->getFlowDataHandler()->getTempDataValue('legal_entity_type'), $registered_number_types)) {
      $this->getFlowDataHandler()->setTempDataValue('registered_number', NULL);
    }

    if (!empty($legal_entity)) {
      $legal_entity->set('registered_name', $this->getFlowDataHandler()->getTempDataValue('registered_name'));
      $legal_entity->set('legal_entity_type', $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'));
      $legal_entity->set('registered_number', $this->getFlowDataHandler()->getTempDataValue('registered_number'));

      if ($legal_entity->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('This %field could not be saved for %form_id');
        $replacements = [
          '%field' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }
    else {
      // Adding a new legal entity.
      $legal_entity = ParDataLegalEntity::create([
        'type' => 'legal_entity',
        'name' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
        'registered_name' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
        'registered_number' => $this->getFlowDataHandler()->getTempDataValue('registered_number'),
        'legal_entity_type' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'),
      ]);
      $legal_entity->save();

      // Now add the legal entity to the organisation.
      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $par_data_organisation = current($par_data_partnership->getOrganisation());
      $par_data_organisation->addLegalEntity($legal_entity);

      if ($par_data_organisation->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('This %field could not be saved for %form_id');
        $replacements = [
          '%field' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }

  }

}
