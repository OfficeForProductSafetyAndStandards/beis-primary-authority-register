<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;

/**
 * The partnership form for the partnership details.
 */
class ParConfirmationReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Review the partnership summary information below';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership $par_data_partnership */
    /** @var ParDataOrganisation $par_data_organisation */
    /** @var ParDataPerson $primary_authority_contact */
    /** @var ParDataPerson $organisation_contact */
    /** @var ParDataPremises $par_data_premises */

    if ($par_data_partnership) {
      $form['partnership_id'] = [
        '#type' => 'hidden',
        '#value' => $par_data_partnership->id(),
      ];

      // Organisation summary.
      $par_data_organisation = current($par_data_partnership->getOrganisation());

      // Display details about the partnership for information.
      $form['about_partnership'] = $this->renderSection('About the partnership', $par_data_partnership, ['about_partnership' => 'about']);

      // Display the authority contacts for information.
      $form['authority_contacts'] = $this->renderSection('Contacts at the Primary Authority', $par_data_partnership, ['field_authority_person' => 'detailed']);

      // Display organisation name and organisation primary address.
      $form['organisation_name'] = $this->renderSection('Organisation name', $par_data_organisation, ['organisation_name' => 'title'], [], TRUE, TRUE);
      $form['organisation_registered_address'] = $this->renderSection('Organisation address', $par_data_organisation, ['field_premises' => 'summary'], [], TRUE, TRUE);

      // Display contacts at the organisation.
      $form['organisation_contacts'] = $this->renderSection('Contacts at the Organisation', $par_data_partnership, ['field_organisation_person' => 'detailed']);

      $form['partnership_info_agreed_authority'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('I confirm I have reviewed the information above'),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues("partnership_info_agreed_authority"),
        '#return_value' => 'on',
      ];
    }
    else {
      $form['help_text'] = [
        '#type' => 'markup',
        '#markup' => $this->t('The partnership could not be created, please contact the Helpdesk if this problem persits.'),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Make sure the confirm box and terms box is ticked.
    if (!$form_state->getValue('partnership_info_agreed_business')) {
      $this->setElementError('partnership_info_agreed_business', $form_state, 'Please confirm you have reviewed the details.');
    }
    if (!$form_state->getValue('terms_organisation_agreed')) {
      $this->setElementError('terms_organisation_agreed', $form_state, 'Please confirm you have read the terms & conditions.');
    }
  }

  public function createEntities() {
    // Load the Authority.
    $cid = $this->getFlowNegotiator()->getFormKey('par_authority_selection');
    $acting_authority = $this->getFlowDataHandler()->getDefaultValues('par_data_authority_id', '', $cid);
    if ($par_data_authority = ParDataAuthority::load($acting_authority)) {
      // Get logged in user ParDataPerson(s) related to the primary authority.
      $authority_person = $this->getParDataManager()->getUserPerson($this->getCurrentUser(), $par_data_authority);
    }

    // Load an existing address if provided.
    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_organisation_suggestion');
    $existing_organisation = $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id','new', $cid);
    if (isset($existing_organisation) && $existing_organisation !== 'new') {
      $par_data_organisation = ParDataOrganisation::load($existing_organisation);

      // Get the address and or contact from the existing organisation.
      if ($par_data_organisation && !$par_data_organisation->get('field_premises')->isEmpty()) {
        $par_data_premises = current($par_data_organisation->get('field_premises')->referencedEntities());
      }
      if ($par_data_organisation && !$par_data_organisation->get('field_person')->isEmpty()) {
        $organisation_person = current($par_data_organisation->get('field_person')->referencedEntities());
      }
    }
    // Create a new organisation but do not save yet.
    else {
      $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_application_organisation');
      $par_data_organisation = ParDataOrganisation::create([
        'type' => 'organisation',
        'organisation_name' => $this->getFlowDataHandler()->getDefaultValues('organisation_name','', $cid),
      ]);
    }

    if (!isset($par_data_premises)) {
      $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_address');
      $par_data_premises = ParDataPremises::create([
        'type' => 'premises',
        'uid' => $this->getCurrentUser()->id(),
        'address' => [
          'country_code' => $this->getFlowDataHandler()->getDefaultValues('country_code', '', $cid),
          'address_line1' => $this->getFlowDataHandler()->getDefaultValues('address_line1','', $cid),
          'address_line2' => $this->getFlowDataHandler()->getDefaultValues('address_line2','', $cid),
          'locality' => $this->getFlowDataHandler()->getDefaultValues('town_city','', $cid),
          'administrative_area' => $this->getFlowDataHandler()->getDefaultValues('county','', $cid),
          'postal_code' => $this->getFlowDataHandler()->getDefaultValues('postcode','', $cid),
        ],
        'nation' => $this->getFlowDataHandler()->getDefaultValues('country','', $cid),
      ]);

      // Add this premises to the organisation.
      if ($par_data_premises->save()) {
        $par_data_organisation->get('field_premises')->appendItem($par_data_premises->id());
        $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_address');
        $par_data_organisation->set('nation', $this->getFlowDataHandler()->getDefaultValues('country','', $cid));
      }
    }
    if (!isset($organisation_person)) {
      $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_contact');
      $email_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_email'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_email']);
      $work_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_phone'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_phone']);
      $mobile_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_mobile'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_mobile']);

      $organisation_person = ParDataPerson::create([
        'type' => 'person',
        'salutation' => $this->getFlowDataHandler()->getDefaultValues('salutation', '', $cid),
        'first_name' => $this->getFlowDataHandler()->getDefaultValues('first_name', '', $cid),
        'last_name' => $this->getFlowDataHandler()->getDefaultValues('last_name', '', $cid),
        'work_phone' => $this->getFlowDataHandler()->getDefaultValues('work_phone', '', $cid),
        'mobile_phone' => $this->getFlowDataHandler()->getDefaultValues('mobile_phone', '', $cid),
        'email' => $this->getFlowDataHandler()->getDefaultValues('email', '', $cid),
        'communication_email' => $email_preference_value,
        'communication_phone' => $work_phone_preference_value,
        'communication_mobile' => $mobile_phone_preference_value,
        'communication_notes' => $this->getFlowDataHandler()->getDefaultValues('notes', '', $cid),
      ]);

      // Add this person to the organisation.
      if ($organisation_person->save()) {
        $par_data_organisation->get('field_person')->appendItem($organisation_person->id());
      }
    }

    // A partnership must have an organisation and an authority to be created.
    if (isset($par_data_organisation) && isset($par_data_authority)
      && $par_data_organisation->save() && $par_data_authority->id()) {
      $cid1 = $this->getFlowNegotiator()->getFormKey('par_partnership_application_type');
      $cid2 = $this->getFlowNegotiator()->getFormKey('par_partnership_about');
      $par_data_partnership = ParDataPartnership::create([
        'type' => 'partnership',
        'uid' => $this->getCurrentUser()->id(),
        'partnership_type' => $this->getFlowDataHandler()->getDefaultValues('application_type', '', $cid1),
        'about_partnership' => $this->getFlowDataHandler()->getDefaultValues('about_partnership', '', $cid2),
        'terms_authority_agreed' => 1,
        'field_authority' => [
          $par_data_authority->id(),
        ],
        'field_organisation' => [
          $par_data_organisation->id(),
        ],
        'field_authority_person' => [
          $authority_person->id(),
        ],
        'field_organisation_person' => [
          $organisation_person->id(),
        ],
      ]);
    }

    return [
      'par_data_partnership' => isset($par_data_partnership) ? $par_data_partnership : NULL,
      'par_data_organisation' => isset($par_data_organisation) ? $par_data_organisation : NULL,
      'par_data_person' => isset($par_data_person) ? $par_data_person : NULL,
      'par_data_premises' => isset($par_data_premises) ? $par_data_premises : NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership $par_data_partnership */
    /** @var ParDataOrganisation $par_data_organisation */
    /** @var ParDataPerson $primary_authority_contact */
    /** @var ParDataPerson $organisation_contact */
    /** @var ParDataPremises $par_data_premises */

    if (isset($par_data_partnership) && $par_data_partnership->save()) {
      // Persist the information in the temporary store here
      // because the application is not yet complete.
      $this->getFlowDataHandler()->setTempDataValue('partnership_id', $par_data_partnership->id());
      return $par_data_partnership;
    }
    else {
      $message = $this->t('Partnership not saved on %form_id');
      $replacements = [
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

  }

}
