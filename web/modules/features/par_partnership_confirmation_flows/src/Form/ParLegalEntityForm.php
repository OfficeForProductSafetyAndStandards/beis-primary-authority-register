<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
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
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_confirmation_add_legal_entity';
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    // Must tell the component plugin where to get data for the selection screen.
    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_select_legal_entities');
    $this->getFlowDataHandler()->setParameter('select_legal_entity_cid', $cid);

    parent::loadData();
  }

}
