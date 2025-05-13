<?php

namespace Drupal\par_profile_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_profile_update_flows\ParFlowAccessTrait;

/**
 * The form for the premises details.
 */
class ParCommunicationPreferencesForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Update communication preferences';

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData() {
    $cid_person_select = $this->getFlowNegotiator()->getFormKey('par_choose_person');
    $person = $this->getFlowDataHandler()->getDefaultValues('user_person', '', $cid_person_select);
    if ($par_data_person = ParDataPerson::load($person)) {
      $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);
    }

    parent::loadData();
  }

  /**
 *
 */
  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

  }

}
