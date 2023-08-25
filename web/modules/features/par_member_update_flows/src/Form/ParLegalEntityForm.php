<?php

namespace Drupal\par_member_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_member_update_flows\ParFlowAccessTrait;

/**
 * Add legal entities to members.
 */
class ParLegalEntityForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Add legal entities';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $par_data_coordinated_business = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business');
    $par_data_organisation = $par_data_coordinated_business->getOrganisation(TRUE);
    $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);

    parent::loadData();
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');
    $legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity');

    // Edit existing legal entity / add new legal entity.
    if ($legal_entity) {
      // Legal entity information may be altered by the registered organisation
      // provider when saving the data.
      $legal_entity->set('registry', $this->getFlowDataHandler()->getTempDataValue('registry'));
      $legal_entity->set('registered_name', $this->getFlowDataHandler()->getTempDataValue('legal_entity_name'));
      $legal_entity->set('legal_entity_type', $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'));
      $legal_entity->set('registered_number', $this->getFlowDataHandler()->getTempDataValue('legal_entity_number'));
    }
    else {
      // Creating the legal entity and using ParDataLegalEntity::lookup() allows
      // information to be retrieved from a registered source like Companies House.
      $par_data_legal_entity = ParDataLegalEntity::create([
        'registry' => $this->getFlowDataHandler()->getTempDataValue('registry'),
        'registered_name' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_name'),
        'legal_entity_type' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'),
        'registered_number' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_number'),
      ]);
    }

    $new = $par_data_legal_entity->isNew();
    $saved = $par_data_legal_entity->save();
    if ($new && $saved) {
      $par_data_organisation->addLegalEntity($par_data_legal_entity);

      $saved = $par_data_organisation->save();
    }

    if ($saved) {
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
