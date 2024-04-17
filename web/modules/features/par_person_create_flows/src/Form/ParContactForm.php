<?php

namespace Drupal\par_person_create_flows\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_person_create_flows\ParFlowAccessTrait;
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
  protected $pageTitle = 'Add contact details';

  /**
   * Get the PAR Role manager.
   */
  public function getParRoleManager(): ParRoleManagerInterface {
    return \Drupal::service('par_roles.role_manager');
  }

  /**
   * Get the entity type manager.
   */
  public function getEntityTypeManager(): EntityTypeManagerInterface {
    return \Drupal::service('entity_type.manager');
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Check to see whether this email address already exists.
    $email = $form_state->getValue('email');
    $existing = $this->getEntityTypeManager()->getStorage('par_data_person')
      ->getQuery('OR')
      ->accessCheck(FALSE)
      ->condition('email', $email)
      ->count();

    if (!empty($email) && $existing->execute() >= 1) {
      $id = $this->getElementId('email', $form);
      $form_state->setErrorByName($this->getElementName(['email']), $this->wrapErrorMessage('There is already a person with this email address, please update the contact record instead.', $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Store the values.
    $this->storeData($form_state);

    // Set the data values on the person.
    $par_data_person = $this->createEntity();

    // Save the updated contact information.
    if ($par_data_person->save()) {
      // Clear the relationships caches once to reset memberships.
      $par_data_person->getRelationships(NULL, NULL, TRUE);
    }
    else {
      $message = $this->t('Person could not be updated for: %account');
      $replacements = [
        '%account' => $par_data_person->id(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }

    // parent::submitForm() must be called after the entity is saved to
    // process the redirection successfully.
    $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);
    parent::submitForm($form, $form_state);

    $this->getFlowDataHandler()->deleteStore();
  }

  /**
   *
   */
  public function createEntity() {
    $par_data_person = ParDataPerson::create([
      'type' => 'person',
      'salutation' => $this->getFlowDataHandler()->getTempDataValue('salutation'),
      'first_name' => $this->getFlowDataHandler()->getTempDataValue('first_name'),
      'last_name' => $this->getFlowDataHandler()->getTempDataValue('last_name'),
      'work_phone' => $this->getFlowDataHandler()->getTempDataValue('work_phone'),
      'mobile_phone' => $this->getFlowDataHandler()->getTempDataValue('mobile_phone'),
      'email' => $this->getFlowDataHandler()->getTempDataValue('email'),
    ]);

    // Set the account for this person.
    if ($account = $this->getParRoleManager()->getAccount($par_data_person)) {
      $par_data_person->setUserAccount($account);
    }

    return $par_data_person;
  }

}
