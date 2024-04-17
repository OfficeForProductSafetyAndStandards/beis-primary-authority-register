<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParPartnershipFlowsLegalEntityForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $this->pageTitle = 'Update Partnership Information | Add a legal entity for your organisation';

    return parent::titleCallback();
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $partnership_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_partnership_le');

    if ($legal_entity = $partnership_legal_entity?->getLegalEntity()) {
      $this->getFlowDataHandler()
        ->setParameter('par_data_legal_entity', $legal_entity);
    }

    parent::loadData();
  }

  /**
   * Validate the form to make sure the correct values have been entered.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Get the partnership.
    /** @var ParDataPartnership $partnership */
    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Creating the legal entity and validate it doesn't already exist.
    $legal_entity = ParDataLegalEntity::create([
      'registry' => $this->getFlowDataHandler()->getTempDataValue('registry'),
      'registered_name' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_name'),
      'legal_entity_type' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'),
      'registered_number' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_number'),
    ]);

    // If this is an existing legal entity check that it is not already active on the partnership.
    $partnership_legal_entities = $partnership->getPartnershipLegalEntities(TRUE);
    if (!$legal_entity->isNew() && !empty($partnership_legal_entities)) {
      // Set start and end dates for the period of the new PLE. If the partnership is
      // not yet active the from_date is NULL, once it is active the from_date is today's date.
      $start_date = $partnership->isActive() ? new DrupalDateTime('now') : NULL;
      $end_date = NULL;

      foreach ($partnership_legal_entities as $partnership_legal_entity) {
        if ($partnership_legal_entity->getLegalEntity()->id() === $legal_entity->id()
            && $partnership_legal_entity->isActiveDuringPeriod($start_date, $end_date)) {
          $id = $this->getElementId(['registered_number'], $form);
          $form_state->setErrorByName($this->getElementName('registered_number'), $this->wrapErrorMessage('This legal entity is already an active participant in the partnership.', $id));
          break;
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity');

    // Edit existing legal entity / add new legal entity.
    if ($legal_entity) {
      // Legal entity information may be altered by the registered organisation
      // provider when saving the data.
      $legal_entity->set('registry', $this->getFlowDataHandler()->getTempDataValue('registry'));
      $legal_entity->set('registered_name', $this->getFlowDataHandler()->getTempDataValue('legal_entity_name'));
      $legal_entity->set('legal_entity_type', $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'));
      $legal_entity->set('registered_number', $this->getFlowDataHandler()->getTempDataValue('legal_entity_number'));

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
      // Legal entity information may be altered by the registered organisation
      // provider when saving the data.
      $legal_entity = ParDataLegalEntity::create([
        'registry' => $this->getFlowDataHandler()->getTempDataValue('registry'),
        'registered_name' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_name'),
        'legal_entity_type' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'),
        'registered_number' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_number'),
      ]);
      $legal_entity->save();

      // Now add the legal entity to the partnership.
      /** @var ParDataPartnership $par_data_partnership */
      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $par_data_partnership->addLegalEntity($legal_entity);

      // Add the new legal entity to the organisation.
      /** @var \Drupal\par_data\Entity\ParDataOrganisation $par_data_organisation */
      $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);
      $par_data_organisation->addLegalEntity($legal_entity);

      // Commit partnership/organisation changes.
      if ($legal_entity->id() && $par_data_partnership->save() && $par_data_organisation->save()) {
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
