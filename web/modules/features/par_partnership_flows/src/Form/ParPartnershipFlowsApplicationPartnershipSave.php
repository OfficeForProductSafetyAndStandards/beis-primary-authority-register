<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

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
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'New Partnership Application';
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

    return parent::buildForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    parent::submitForm($form, $form_state);

    if ($this->getFlowName() === 'partnership_application') {

      // Load the Authority.
      $par_data_authority = ParDataAuthority::load($this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection'));
      $par_data_premises = [];

      // Get logged in user ParDataPerson related to the primary authority.
      $authority_person = ParDataPerson::load($this->parDataManager->getUserPerson($this->getCurrentUser(), $par_data_authority)[0]);

      $organisation_id = $this->getDefaultValues('par_data_organisation_id','', 'par_partnership_organisation_suggestion');

      if ($organisation_id === 'new') {

        // Save Main Contact.
        $organisation_person = ParDataPerson::create([
          'type' => 'person',
        ]);

        $organisation_person->set('salutation', $this->getDefaultValues('salutation', '', 'par_partnership_contact'));
        $organisation_person->set('first_name', $this->getDefaultValues('first_name', '', 'par_partnership_contact'));
        $organisation_person->set('last_name', $this->getDefaultValues('last_name', '', 'par_partnership_contact'));
        $organisation_person->set('work_phone', $this->getDefaultValues('work_phone', '', 'par_partnership_contact'));
        $organisation_person->set('mobile_phone', $this->getDefaultValues('mobile_phone', '', 'par_partnership_contact'));
        $organisation_person->set('email', $this->getDefaultValues('email', '', 'par_partnership_contact'));
        $organisation_person->set('communication_notes', $this->getDefaultValues('notes', '', 'par_partnership_contact'));

        // Save the email preference.
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
          $message = $this->t('ParDataPerson %email could not be created for %form_id');
          $replacements = [
            '%email' => $this->getDefaultValues('email', '', 'par_partnership_contact'),
            '%form_id' => $this->getFormId(),
          ];
          $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
        }

        // Save organisation premises.
        $par_data_premises = ParDataPremises::create([
          'type' => 'premises',
          'uid' => $this->getCurrentUser()->id(),
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
        $par_data_organisation = ParDataOrganisation::create([
          'type' => 'organisation',
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
      }
      else {
        $par_data_organisation = ParDataOrganisation::load($organisation_id);
        $organisation_person = current($par_data_organisation->getPerson());

        // If Main Contact does not exist, save one from the Main Contact form.
        if (!$organisation_person->id()) {
          $organisation_person = ParDataPerson::create([
            'type' => 'person',
          ]);

          $organisation_person->set('salutation', $this->getDefaultValues('salutation', '', 'par_partnership_contact'));
          $organisation_person->set('first_name', $this->getDefaultValues('first_name', '', 'par_partnership_contact'));
          $organisation_person->set('last_name', $this->getDefaultValues('last_name', '', 'par_partnership_contact'));
          $organisation_person->set('work_phone', $this->getDefaultValues('work_phone', '', 'par_partnership_contact'));
          $organisation_person->set('mobile_phone', $this->getDefaultValues('mobile_phone', '', 'par_partnership_contact'));
          $organisation_person->set('email', $this->getDefaultValues('email', '', 'par_partnership_contact'));
          $organisation_person->set('communication_notes', $this->getDefaultValues('notes', '', 'par_partnership_contact'));

          // Save the email preference.
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
            $message = $this->t('ParDataPerson %email could not be created for %form_id');
            $replacements = [
              '%email' => $this->getDefaultValues('email', '', 'par_partnership_contact'),
              '%form_id' => $this->getFormId(),
            ];
            $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
          }

          // Add to organisation `field_person`.
          $par_data_organisation->get('field_person')->appendItem($organisation_person->id());
          $par_data_organisation->save();
        }
      }

      // Save partnership.
      $partnership = ParDataPartnership::create([
        'type' => 'partnership',
        'uid' => $this->getCurrentUser()->id(),
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
        $this->deleteStore();
        $form_state->setRedirect($this->getFlow()->getNextRoute('save'), ['par_data_partnership' => $partnership->id()]);
      }
      else {
        $message = $this->t('Partnership not saved on %form_id');
        $replacements = [
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }

    }

    $this->addCacheableDependency($authority_person);
    $this->addCacheableDependency($organisation_person);
    $this->addCacheableDependency($par_data_authority);
    $this->addCacheableDependency($par_data_organisation);
    $this->addCacheableDependency($par_data_premises);
    $this->addCacheableDependency($organisation_id);

  }

}
