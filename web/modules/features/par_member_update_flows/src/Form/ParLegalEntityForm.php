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

    $par_data_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity') ?: ParDataLegalEntity::create([
      'type' => 'legal_entity',
    ]);

    $par_data_legal_entity->set('name', $this->getFlowDataHandler()->getTempDataValue('registered_name'));
    $par_data_legal_entity->set('registered_name', $this->getFlowDataHandler()->getTempDataValue('registered_name'));
    $par_data_legal_entity->set('registered_number', $this->getFlowDataHandler()->getTempDataValue('registered_number'));
    $par_data_legal_entity->set('legal_entity_type', $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'));

    $new = $par_data_legal_entity->isNew();
    $saved = $par_data_legal_entity->save();
    if ($new && $saved) {
      $par_data_organisation->get('field_legal_entity')->appendItem($par_data_legal_entity);

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
