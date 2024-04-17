<?php

namespace Drupal\par_profile_update_flows\Form;

use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_profile_update_flows\ParFlowAccessTrait;

/**
 * The member contact form.
 */
class ParContactForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Update contact details';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $account = $this->getFlowDataHandler()->getParameter('user');

    $cid_person_select = $this->getFlowNegotiator()->getFormKey('par_choose_person');
    $person = $this->getFlowDataHandler()->getDefaultValues('user_person', '', $cid_person_select);
    $par_data_person = !empty($person) ? ParDataPerson::load($person) : NULL;

    // If no profile record could be found then create a new one.
    if (!$par_data_person instanceof ParDataPerson) {
      $par_data_person = ParDataPerson::create([
        'type' => 'person',
      ]);
      $par_data_person->updateEmail($account?->getEmail());
    }

    $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);

    parent::loadData();
  }

}
