<?php

namespace Drupal\par_person_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_person_update_flows\ParFlowAccessTrait;
use Drupal\par_roles\ParRoleManagerInterface;

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
   * Get the PAR Role manager.
   */
  public function getParRoleManager(): ParRoleManagerInterface {
    return \Drupal::service('par_roles.role_manager');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $account = $par_data_person->getUserAccount();
      $this->getFlowDataHandler()->setParameter('user', $account);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the data values on the person.
    $par_data_person = $this->createEntity();

    // Save the updated contact information.
    if ($par_data_person->save()) {
      // Clear the relationships caches once to reset memberships.
      $par_data_person->getRelationships(NULL, NULL, TRUE);

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('Person could not be updated for: %account');
      $replacements = [
        '%account' => $par_data_person->id(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

  /**
   *
   */
  public function createEntity() {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    if ($par_data_person) {
      // Store the original email to check if it changes.
      $this->getFlowDataHandler()->setFormPermValue('original_email', $par_data_person->getEmail());

      // Set the account for this person.
      if ($account = $this->getParRoleManager()->getAccount($par_data_person)) {
        $par_data_person->setUserAccount($account);
      }

      // Update the person record with the new values.
      $par_data_person->set('salutation', $this->getFlowDataHandler()->getTempDataValue('salutation'));
      $par_data_person->set('first_name', $this->getFlowDataHandler()->getTempDataValue('first_name'));
      $par_data_person->set('last_name', $this->getFlowDataHandler()->getTempDataValue('last_name'));
      $par_data_person->set('work_phone', $this->getFlowDataHandler()->getTempDataValue('work_phone'));
      $par_data_person->set('mobile_phone', $this->getFlowDataHandler()->getTempDataValue('mobile_phone'));

      // Update the email address.
      $email = $this->getFlowDataHandler()->getTempDataValue('email');
      if (!empty($email)) {
        $par_data_person->updateEmail($email);
      }
    }

    return $par_data_person;
  }

}
