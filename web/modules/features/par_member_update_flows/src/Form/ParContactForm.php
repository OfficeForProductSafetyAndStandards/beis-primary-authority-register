<?php

namespace Drupal\par_member_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_update_flows\ParFlowAccessTrait;

/**
 * The member contact form.
 */
class ParContactForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Add member contact details';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_coordinated_business = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business');
    $par_data_organisation = $par_data_coordinated_business->getOrganisation(TRUE);

    $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);
    $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);
    $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_organisation->getPerson(TRUE));
    parent::loadData();
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    if ($par_data_person) {
      $par_data_person->set('salutation', $this->getFlowDataHandler()->getTempDataValue('salutation'));
      $par_data_person->set('first_name', $this->getFlowDataHandler()->getTempDataValue('first_name'));
      $par_data_person->set('last_name', $this->getFlowDataHandler()->getTempDataValue('last_name'));
      $par_data_person->set('work_phone', $this->getFlowDataHandler()->getTempDataValue('work_phone'));
      $par_data_person->set('mobile_phone', $this->getFlowDataHandler()->getTempDataValue('mobile_phone'));
      $par_data_person->set('email', $this->getFlowDataHandler()->getTempDataValue('email'));
      $par_data_person->set('communication_notes', $this->getFlowDataHandler()->getTempDataValue('notes'));

      if ($par_data_person->save()) {
        $this->getFlowDataHandler()->deleteStore();
      } else {
        $message = $this->t('This %person could not be saved for %form_id');
        $replacements = [
          '%person' => $this->getFlowDataHandler()->getTempDataValue('last_name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }
  }

}
