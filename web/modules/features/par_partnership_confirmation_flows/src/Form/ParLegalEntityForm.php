<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParFlowAccessTrait;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParLegalEntityForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Confirm the legal entity';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    // Must tell the component plugin where to get data for the selection screen.
    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_select_legal_entities');
    $this->getFlowDataHandler()->setParameter('select_legal_entity_cid', $cid);

    parent::loadData();
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $existing_legal_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_select_legal_entities');
    $existing_legal_entities = $this->getFlowDataHandler()->getTempDataValue('field_legal_entity', $existing_legal_cid) ?: [];

    $par_data_legal_entities_existing = [];
    foreach ($existing_legal_entities as $delta => $existing_legal_entity) {
      $par_data_legal_entities_existing[$delta] = ParDataLegalEntity::load($existing_legal_entity);
    }

    $existing = NestedArray::filter($par_data_legal_entities_existing);

    // Only require new legal entities if the existing ones are empty.
    if (empty($existing)) {
      parent::validateForm($form, $form_state);
    }
  }

}
