<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
//use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\Entity\User;

/**
 * Save the partnership application
 */
class ParPartnershipFlowsApplicationPartnershipSave extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_application_save';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataPerson $par_data_person
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $this->retrieveEditableValues();

    $form['blah'] = [
      '#type' => 'markup',
      '#markup' => 'This form is to self-submit.'
    ];

    // @todo implement self-submit.

    return parent::buildForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    if ($this->getFlowName() == 'partnership_application') {

      // Load the Authority.
      $par_data_authority = ParDataAuthority::load($this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection'));

      // @todo see possibility of injecting AccountProxy.
      $user = User::load(\Drupal::currentUser()->id());

      // get authority person.
      $authority_person = ParDataPerson::load($this->parDataManager->getUserPerson($user, $par_data_authority)[0]);

      $organisation_id = $this->getDefaultValues('par_data_organisation_id','', 'par_partnership_organisation_suggestion');

      if ($organisation_id === 'new') {

        // Save Main Contact.
        $organisation_person = \Drupal\par_data\Entity\ParDataPerson::create([
          'type' => 'person',
          'uid' => 1,
        ]);

        $organisation_person->set('salutation', $this->getDefaultValues('salutation', '', 'par_partnership_contact'));
        $organisation_person->set('first_name', $this->getDefaultValues('first_name', '', 'par_partnership_contact'));
        $organisation_person->set('last_name', $this->getDefaultValues('last_name', '', 'par_partnership_contact'));
        $organisation_person->set('work_phone', $this->getDefaultValues('work_phone', '', 'par_partnership_contact'));
        $organisation_person->set('mobile_phone', $this->getDefaultValues('mobile_phone', '', 'par_partnership_contact'));
        $organisation_person->set('email', $this->getDefaultValues('email', '', 'par_partnership_contact'));
        $organisation_person->set('communication_notes', $this->getDefaultValues('notes', '', 'par_partnership_contact'));

        $email_preference_value = isset($this->getTempDataValue('preferred_contact', 'par_partnership_contact')['communication_email'])
          && !empty($this->getTempDataValue('preferred_contact', 'par_partnership_contact')['communication_email']);
        $organisation_person->set('communication_email', $email_preference_value);
        // Save the work phone preference.
        $work_phone_preference_value = isset($this->getTempDataValue('preferred_contact', 'par_partnership_contact')['communication_phone'])
          && !empty($this->getTempDataValue('preferred_contact', 'par_partnership_contact')['communication_phone']);
        $organisation_person->set('communication_phone', $work_phone_preference_value);
        // Save the mobile phone preference.
        $mobile_phone_preference_value = isset($this->getTempDataValue('preferred_contact', 'par_partnership_contact')['communication_mobile'])
          && !empty($this->getTempDataValue('preferred_contact', 'par_partnership_contact')['communication_mobile']);
        $organisation_person->set('communication_mobile', $mobile_phone_preference_value);

        if (!$organisation_person->save()) {
          $message = $this->t('This %person could not be saved for %form_id');
          $replacements = [
            '%person' => $this->getTempDataValue('name'),
            '%form_id' => $this->getFormId(),
          ];
          $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
        }

        // Save business premises.
        $par_data_premises = \Drupal\par_data\Entity\ParDataPremises::create([
          'type' => 'premises',
          // 'name' => $this->getTempDataValue('salutation'),
          'uid' => \Drupal::currentUser()->id(),
          'address' => [
            'country_code' => $this->getDefaultValues('country_code', '', 'par_partnership_address'),
            'address_line1' => $this->getDefaultValues('address_line1','', 'par_partnership_address'),
            'address_line2' => $this->getDefaultValues('address_line2','', 'par_partnership_address'),
            'locality' => $this->getDefaultValues('town_city','', 'par_partnership_address'),
            'administrative_area' => $this->getDefaultValues('county','', 'par_partnership_address'),
            'postal_code' => $this->getDefaultValues('postcode','', 'par_partnership_address'),
          ],
          'nation' => 'GB-SCT',
        ]);
        $par_data_premises->save();

        // Save organisation.
        $par_data_organisation = \Drupal\par_data\Entity\ParDataOrganisation::create([
          'type' => 'organisation',
          'name' => $this->getDefaultValues('organisation_name','', 'par_partnership_application_organisation_search'),
          'uid' => \Drupal::currentUser()->id(),
          'organisation_name' => $this->getDefaultValues('organisation_name','', 'par_partnership_application_organisation_search'),
          'nation' => 'GB-SCT',
          'premises_mapped' => TRUE,
          'field_person' => [
            $organisation_person->id(),
          ],
          'field_premises' => [
            $par_data_premises->id(),
          ],
        ]);

        $par_data_organisation->save();
      } else {
        $par_data_organisation = ParDataOrganisation::load($organisation_id);
        $organisation_person = $par_data_organisation->get('field_person')->first();
      }

      // Save partnership.
      $partnership = \Drupal\par_data\Entity\ParDataPartnership::create([
        'type' => 'partnership',
        'name' => 'Partnership Name?',
        'uid' => \Drupal::currentUser()->id(),
        'partnership_type' => $this->getDefaultValues('application_type', '', 'par_partnership_application_type'),
        'partnership_status' => 'application',
        'about_partnership' => $this->getDefaultValues('about_partnership', '', 'par_partnership_about'),
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

      if ($partnership->save()) {
        $form_state->setRedirect($this->getFlow()->getNextRoute('save'), ['par_data_partnership' => $partnership->id()]);
        $this->deleteStore();
      }

    }

  }

}
