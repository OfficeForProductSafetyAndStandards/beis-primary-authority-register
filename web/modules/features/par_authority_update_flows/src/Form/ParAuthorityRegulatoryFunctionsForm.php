<?php

namespace Drupal\par_authority_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_authority_update_flows\ParFlowAccessTrait;

/**
 * The regulatory functions update form.
 */
class ParAuthorityRegulatoryFunctionsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Provided Regulatory Functions';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $par_data_authority = NULL) {
    // Change the secondary action to back.
    $this->getFlowNegotiator()->getFlow()->setActions(['save', 'back']);

    return parent::buildForm($form, $form_state);
  }

}
