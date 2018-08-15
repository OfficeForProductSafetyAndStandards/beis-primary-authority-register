<?php

namespace Drupal\par_profile_update_flows\Form;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_profile_update_flows\ParFlowAccessTrait;

/**
 * The form for choosing which contact details to edit.
 */
class ParChooseContactForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Choose which contact to update';

  /**
   * {@inheritdoc}
   */
  public function loadData() {

    parent::loadData();
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);


  }

}
