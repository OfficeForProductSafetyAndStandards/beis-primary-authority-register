<?php

namespace Drupal\par_partnership_contact_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataMembershipInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\Plugin\ParForm\ParDedupePersonForm;
use Drupal\par_partnership_contact_add_flows\ParFlowAccessTrait;

/**
 * The form for the partnership details.
 */
class ParReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Review contact information';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    // Set the data values on the entities.
    $entities = $this->createEntities();
    extract($entities);
    /** @var \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership */
    /** @var \Drupal\par_data\Entity\ParDataPerson $par_data_person */

    $type = $this->getFlowDataHandler()->getParameter('type');

    $this->getFlowDataHandler()->setFormPermValue("institution", $type);
    if ($par_data_partnership) {
      $this->getFlowDataHandler()
        ->setFormPermValue("partnership", lcfirst($par_data_partnership->label()));
    }

    if (isset($par_data_person)) {
      $this->getFlowDataHandler()
        ->setFormPermValue("full_name", $par_data_person->getFullName());
      $this->getFlowDataHandler()
        ->setFormPermValue("work_phone", $par_data_person->getWorkPhone());
      $this->getFlowDataHandler()
        ->setFormPermValue("mobile_phone", $par_data_person->getMobilePhone());
      $this->getFlowDataHandler()
        ->setFormPermValue("email", $par_data_person->getEmailWithPreferences());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $form['partnership'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'govuk-form-group'],
      [
        '#markup' => $this->t('The following person will be added to the @partnership.',
          ['@partnership' => $this->getFlowDataHandler()->getFormPermValue("partnership")]),
      ],
    ];

    $form['personal'] = [
      '#type' => 'fieldset',
      'name' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'govuk-form-group'],
        '#title' => 'Name',
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('full_name', '(none)'),
        ],
      ],
    ];

    $form['contact_details'] = [
      '#type' => 'fieldset',
      'email' => [
        '#type' => 'fieldset',
        '#title' => 'Email',
        '#attributes' => ['class' => 'govuk-form-group'],
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('email', '(none)'),
        ],
      ],
      'work_phone' => [
        '#type' => 'fieldset',
        '#title' => 'Work phone',
        '#attributes' => ['class' => 'govuk-form-group'],
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('work_phone', '(none)'),
        ],
      ],
      'mobile_phone' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'govuk-form-group'],
        '#title' => 'Mobile phone',
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('mobile_phone', '(none)'),
        ],
      ],
    ];

    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['save', 'cancel']);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function createEntities() {
    // Get the cache IDs for the various forms that needs to be extracted from.
    $contact_details_cid = $this->getFlowNegotiator()->getFormKey('par_add_contact');
    $contact_dedupe_cid = $this->getFlowNegotiator()->getFormKey('dedupe_contact');

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    $deduped_contact = $this->getFlowDataHandler()->getDefaultValues('contact_record', NULL, $contact_dedupe_cid);
    $par_data_person = ParDedupePersonForm::getDedupedPerson($deduped_contact);

    // Create the new person.
    if (!$par_data_person) {
      $par_data_person = ParDataPerson::create([
        'type' => 'person',
        'salutation' => $this->getFlowDataHandler()->getTempDataValue('salutation', $contact_details_cid),
        'first_name' => $this->getFlowDataHandler()->getTempDataValue('first_name', $contact_details_cid),
        'last_name' => $this->getFlowDataHandler()->getTempDataValue('last_name', $contact_details_cid),
        'work_phone' => $this->getFlowDataHandler()->getTempDataValue('work_phone', $contact_details_cid),
        'mobile_phone' => $this->getFlowDataHandler()->getTempDataValue('mobile_phone', $contact_details_cid),
      ]);

      // Update the email address.
      $email = $this->getFlowDataHandler()->getTempDataValue('email', $contact_details_cid);
      if (!empty($email)) {
        $par_data_person->updateEmail($email);
      }

      if ($communication_notes = $this->getFlowDataHandler()->getTempDataValue('notes', $contact_details_cid)) {
        $par_data_person->set('communication_notes', $communication_notes);
      }

      if ($preferred_contact = $this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_details_cid)) {
        $email_preference_value = !empty($preferred_contact['communication_email']);
        $par_data_person->set('communication_email', $email_preference_value);

        // Save the work phone preference.
        $work_phone_preference_value = !empty($preferred_contact['communication_phone']);
        $par_data_person->set('communication_phone', $work_phone_preference_value);

        // Save the mobile phone preference.
        $mobile_phone_preference_value = !empty($preferred_contact['communication_mobile']);
        $par_data_person->set('communication_mobile', $mobile_phone_preference_value);
      }
    }

    $entities = [
      'par_data_partnership' => $par_data_partnership,
      'par_data_person' => $par_data_person,
    ];

    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the data values on the entities.
    $entities = $this->createEntities();
    extract($entities);
    /** @var \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership */
    /** @var \Drupal\par_data\Entity\ParDataPerson $par_data_person */

    $type = $this->getFlowDataHandler()->getParameter('type');

    if ($par_data_person->save()) {
      // Add this person to the partnership and the appropriate authority or organisation.
      switch ($type) {
        case 'authority':
          $field = 'field_authority_person';
          $par_data_authority = $par_data_partnership->getAuthority(TRUE);
          if ($par_data_authority instanceof ParDataMembershipInterface) {
            $par_data_authority->get('field_person')->appendItem($par_data_person);
            $par_data_authority->save();
          }

          break;

        case 'organisation':
          $field = 'field_organisation_person';
          $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);
          if ($par_data_organisation instanceof ParDataMembershipInterface) {
            $par_data_organisation->get('field_person')->appendItem($par_data_person);
            $par_data_organisation->save();
          }

          break;

      }
      if (isset($field)) {
        $par_data_partnership->get($field)->appendItem($par_data_person);
        $par_data_partnership->save();
      }

      // Re-save the user account to store any roles that might have been added,
      // but also to clear the user account caches.
      if (isset($account)) {
        $account->save();
        \Drupal::entityTypeManager()->getStorage('user')->resetCache([$account->id()]);
      }

      // We also need to clear the relationships caches once
      // any new relationships have been saved.
      $par_data_person->getRelationships(NULL, NULL, TRUE);

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('Person could not be created for: %account');
      $replacements = [
        '%account' => $par_data_person->id(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }

    // Go to cancel route.
    switch ($type) {
      case 'organisation':
        $cancel_route = 'par_partnership_flows.organisation_details';

        break;

      case 'authority':
        $cancel_route = 'par_partnership_flows.authority_details';

        break;
    }

    if ($cancel_route) {
      $form_state->setRedirect($cancel_route, ['par_data_partnership' => $par_data_partnership->id()]);
    }
  }

}
