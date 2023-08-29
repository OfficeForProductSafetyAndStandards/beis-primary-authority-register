<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Person de-duplication form plugin.
 *
 * @ParForm(
 *   id = "person_dedupe",
 *   title = @Translation("De-duplicate people.")
 * )
 */
class ParDedupePersonForm extends ParFormPluginBase {

  const ADD_NEW = 'add_new';

  /**
   * A helper method to extract the deduped contact from the selections.
   *
   * @param mixed $value
   *   The data value to be checked for.
   *
   * @return mixed
   *   A user account if selected, otherwise null.
   */
  static function getDedupedPerson($value) {
    foreach ([self::ADD_NEW] as $opt) {
      if ($value === $opt) {
        return NULL;
      }
    }

    $par_data_person = $value ? ParDataPerson::load($value) : NULL;

    return $par_data_person;
  }

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    $cid_contact_details = $this->getFlowNegotiator()->getFormKey('email_address');
    $contact_email = $this->getFlowDataHandler()->getDefaultValues('email', NULL, $cid_contact_details);

    $contact_records = [];

    // Get the person entities that represent a given user.
    if ($contact_email) {
      $people = \Drupal::entityTypeManager()
        ->getStorage('par_data_person')
        ->loadByProperties(['email' => $contact_email]);

      $contact_records = $this->getParDataManager()->getEntitiesAsOptions($people, $contact_records, 'summary');
    }

    // Pre-select a person if they have already been choosen
    // and they are in the list of available choices.
    if ($par_data_person && isset($user_people[$par_data_person->id()])) {
      $this->getFlowDataHandler()->setFormPermValue('contact_record', $par_data_person->id());
    }

    $this->getFlowDataHandler()->setFormPermValue('contact_records', $contact_records);

    // Choose whether to allow a new contact to be created
    // if an existing one is found.
    $require_existing = isset($this->getConfiguration()['require_existing']) ? (bool) $this->getConfiguration()['require_existing'] : FALSE;
    if ($require_existing) {
      $this->getFlowDataHandler()->setFormPermValue('require_existing', $require_existing);
    }
    else {
      // Allow the add message to be configured.
      $add_message = isset($this->getConfiguration()['add_message']) ? (string) $this->getConfiguration()['add_message'] : "no, create a new contact";
      $this->getFlowDataHandler()->setFormPermValue('add_message', $add_message);
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Get all the allowed authorities.
    $contact_records = $this->getFlowDataHandler()->getFormPermValue('contact_records');
    $require_existing = (bool) $this->getFlowDataHandler()->getFormPermValue('require_existing');
    $add_message = $this->getFlowDataHandler()->getFormPermValue('add_message');

    // If there are no people we should treat it as a new record and skip.
    if (count($contact_records) <= 0) {
      $this->getFlowDataHandler()->setTempDataValue('contact_record', self::ADD_NEW);
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }
    // If there is only one person to choose submit the form automatically and go to the next step.
    elseif (count($contact_records) === 1 && $require_existing) {
      $this->getFlowDataHandler()->setTempDataValue('contact_record', key($contact_records));
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    $form['intro'] = [
      '#type' => 'markup',
      '#markup' => "We have found multiple contacts that match the information you have entered, please choose the contact record that best matches.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Add the ability to add a new contact record.
    if (!$require_existing) {
      $contact_records[self::ADD_NEW] = $add_message;
    }
    $form['contact_record'] = [
      '#type' => 'radios',
      '#title' => t('Choose which contact record you would like to use'),
      '#options' => $contact_records,
      '#default_value' => $this->getDefaultValuesByKey("contact_record", $index, NULL),
      '#attributes' => ['class' => ['form-group']],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $person_id_key = $this->getElementKey('contact_record');
    if (empty($form_state->getValue($person_id_key))) {
      $id_key = $this->getElementKey('contact_record', $index, TRUE);
      $form_state->setErrorByName($this->getElementName($person_id_key), $this->wrapErrorMessage('You must select a contact record.', $this->getElementId($id_key, $form)));
    }

    parent::validate($form, $form_state, $index, $action);
  }
}
