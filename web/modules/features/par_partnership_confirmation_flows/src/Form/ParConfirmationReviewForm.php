<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_partnership_confirmation_flows\ParFlowAccessTrait;

/**
 * The partnership form for the partnership details.
 */
class ParConfirmationReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Review the partnership summary information below';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Override the route parameter so that data loaded will be from this entity.
    $this->getFlowDataHandler()->setParameter('partnership_info_agreed_business', $par_data_partnership->getBoolean('partnership_info_agreed_business'));
    $this->getFlowDataHandler()->setParameter('terms_organisation_agreed', $par_data_partnership->getBoolean('terms_organisation_agreed'));

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $form['partnership_id'] = [
      '#type' => 'hidden',
      '#value' => $par_data_partnership->id(),
    ];

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership $par_data_partnership */
    /** @var ParDataOrganisation $par_data_organisation */
    /** @var ParDataPerson $par_data_person */
    /** @var ParDataPremises $par_data_premises */
    /** @var ParDataLegalEntity[] $par_data_legal_entities */
    /** @var ParDataLegalEntity[] $par_data_legal_entities_existing */

    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());

    // Display details about the organisation for information.
    $form['about_organisation'] = $this->renderSection('About the organisation', $par_data_organisation, ['comments' => 'about']);
    $form['about_organisation']['comments']['operations']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()
          ->getLinkByCurrentOperation('about_business', [], ['query' => ['destination' => $return_path]])
          ->setText('Change the details about this partnership')
          ->toString(),
      ]),
    ];

    // Display organisation name.
    $form['organisation_name'] = $this->renderSection('Organisation name', $par_data_organisation, ['organisation_name' => 'title'], [], TRUE, TRUE);

    // Display the organisation's primary address.
    $form['organisation_registered_address'] = $this->renderSection('Organisation address', $par_data_organisation, ['field_premises' => 'summary'], [], TRUE, TRUE);
    $form['organisation_registered_address']['field_premises']['operations']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()
          ->getLinkByCurrentOperation('address', [], ['query' => ['destination' => $return_path]])
          ->setText('Change the primary address')
          ->toString(),
      ]),
    ];

    // Display contacts at the organisation.
    $form['organisation_contacts'] = $this->renderSection('Contacts at the Organisation', $par_data_partnership, ['field_organisation_person' => 'detailed'], [],  TRUE, TRUE);
    $form['organisation_contacts']['field_organisation_person']['operations']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()
          ->getLinkByCurrentOperation('contact', [], ['query' => ['destination' => $return_path]])
          ->setText('Change the main contact')
          ->toString(),
      ]),
    ];

    // Display SIC code, number of employees.
    $form['sic_code'] = $this->renderSection('Primary SIC code', $par_data_organisation, ['field_sic_code' => 'detailed'], [], TRUE, TRUE);
    $form['sic_code']['field_sic_code']['operations']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()
          ->getLinkByCurrentOperation('sic_code', [], ['query' => ['destination' => $return_path]])
          ->setText('Change the SIC code')
          ->toString(),
      ]),
    ];

    if ($par_data_partnership->isDirect()) {
      // Display the number of employees.
      $form['number_employees'] = $this->renderSection('Number of employees at the organisation', $par_data_organisation, ['employees_band' => 'detailed']);
      $form['number_employees']['employees_band']['operations']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlowNegotiator()->getFlow()
            ->getLinkByCurrentOperation('employee_number', [], ['query' => ['destination' => $return_path]])
            ->setText('Change the number of employees')
            ->toString(),
        ]),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ];
    }
    if ($par_data_partnership->isCoordinated()) {
      // Display the size of the coordinator.
      $form['number_members'] = $this->renderSection('Number of members', $par_data_organisation, ['size' => 'detailed']);
      $form['number_members']['size']['operations']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlowNegotiator()->getFlow()
            ->getLinkByCurrentOperation('business_size', [], ['query' => ['destination' => $return_path]])
            ->setText('Change the size of the business')
            ->toString(),
        ]),
      ];
    }

    // Display legal entities.
    $legal_entities = array_filter($par_data_legal_entities_existing + $par_data_legal_entities);
    $form['legal_entities'] = $this->renderEntities('Legal entities', $legal_entities);

    // Display the links to change legal entities
    if (!empty($par_data_organisation->getLegalEntity())) {
      $form['legal_entities_select_link'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlowNegotiator()->getFlow()
            ->getLinkByCurrentOperation('legal_select', [], ['query' => ['destination' => $return_path]])
            ->setText('Change existing legal entities added to the organisation')
            ->toString(),
        ]),
      ];
    }
    $form['legal_entities_new_link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()
          ->getLinkByCurrentOperation('legal_add', [], ['query' => ['destination' => $return_path]])
          ->setText('Change the legal entities')
          ->toString(),
      ]),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Display trading names.
    $form['trading_names'] = $this->renderSection('Trading names', $par_data_organisation, ['trading_name' => 'full']);
    $form['trading_names']['trading_name']['operations']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()
          ->getLinkByCurrentOperation('trading_name', [], ['query' => ['destination' => $return_path]])
          ->setText('Change the trading name')
          ->toString(),
      ]),
    ];

    $form['partnership_info_agreed_business'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I confirm I have reviewed the information above'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("partnership_info_agreed_business"),
      '#return_value' => 'on',
    ];

    $url = Url::fromUri('internal:/par-terms-and-conditions');
    $terms_link = Link::fromTextAndUrl(t('Terms & Conditions (opens in a new window)'), $url);
    $form['terms_organisation_agreed'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I have read and agree to the %terms.', ['%terms' => $terms_link->toString()]),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("terms_organisation_agreed"),
      '#return_value' => 'on',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Make sure the confirm box and terms box is ticked.
    if (!$form_state->getValue('partnership_info_agreed_business')) {
      $message = $this->wrapErrorMessage('Please confirm you have reviewed the details.', $this->getElementId('partnership_info_agreed_business', $form));
      $form_state->setErrorByName($this->getElementName('partnership_info_agreed_business'), $message);

    }
    if (!$form_state->getValue('terms_organisation_agreed')) {
      $message = $this->wrapErrorMessage('Please confirm you have read the terms & conditions.', $this->getElementId('terms_organisation_agreed', $form));
      $form_state->setErrorByName($this->getElementName('terms_organisation_agreed'), $message);
    }

    // Validate that there are actually some legal entities set.
    $legal_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_add_legal_entity');
    $existing_legal_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_select_legal_entities');

    $legal_entities = $this->getFlowDataHandler()->getTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'legal_entity', $legal_cid) ?: [];
    $existing_legal_entities = $this->getFlowDataHandler()->getTempDataValue('field_legal_entity', $existing_legal_cid) ?: [];

    if (empty($legal_entities) && empty($existing_legal_entities)) {
      $message = $this->wrapErrorMessage('You must add at least one legal entity to complete this partnership.', $this->getElementId('legal_entities_new_link', $form));
      $form_state->setErrorByName($this->getElementName('legal_entities_new_link'), $message);
    }
  }

  public function createEntities() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);
    if (empty($par_data_organisation)) {
      $par_data_organisation = ParDataOrganisation::create();
    }

    // Set the data for the about form.
    $about_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_about_business');
    $par_data_organisation->set('comments', $this->getFlowDataHandler()->getTempDataValue('about_business', $about_cid));

    // Set the data for the address form.
    $par_data_premises = $par_data_organisation->getPremises(TRUE);
    if (empty($par_data_premises)) {
      $par_data_premises = ParDataPremises::create();
    }

    $address_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_address');
    $address = [
      'country_code' => $this->getFlowDataHandler()->getTempDataValue('country_code', $address_cid),
      'address_line1' => $this->getFlowDataHandler()->getTempDataValue('address_line1', $address_cid),
      'address_line2' => $this->getFlowDataHandler()->getTempDataValue('address_line2', $address_cid),
      'locality' => $this->getFlowDataHandler()->getTempDataValue('town_city', $address_cid),
      'administrative_area' => $this->getFlowDataHandler()->getTempDataValue('county', $address_cid),
      'postal_code' => $this->getFlowDataHandler()->getTempDataValue('postcode', $address_cid),
    ];
    $par_data_premises->set('address', $address);
    $par_data_premises->set('nation', $this->getFlowDataHandler()->getTempDataValue('country', $address_cid));

    // Set the data for the contact form.
    $par_data_person = $par_data_partnership->getOrganisationPeople(TRUE);
    if (empty($par_data_person)) {
      $par_data_person = ParDataPerson::create();
    }

    $contact_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_contact');
    $par_data_person->set('salutation', $this->getFlowDataHandler()->getTempDataValue('salutation', $contact_cid));
    $par_data_person->set('first_name', $this->getFlowDataHandler()->getTempDataValue('first_name', $contact_cid));
    $par_data_person->set('last_name', $this->getFlowDataHandler()->getTempDataValue('last_name', $contact_cid));
    $par_data_person->set('work_phone', $this->getFlowDataHandler()->getTempDataValue('work_phone', $contact_cid));
    $par_data_person->set('mobile_phone', $this->getFlowDataHandler()->getTempDataValue('mobile_phone', $contact_cid));
    $par_data_person->set('email', $this->getFlowDataHandler()->getTempDataValue('email', $contact_cid));
    $par_data_person->set('communication_notes', $this->getFlowDataHandler()->getTempDataValue('notes', $contact_cid));
    $email_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_cid)['communication_email'])
      && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_cid)['communication_email']);
    $par_data_person->set('communication_email', $email_preference_value);
    $work_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_cid)['communication_phone'])
      && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_cid)['communication_phone']);
    $par_data_person->set('communication_phone', $work_phone_preference_value);
    $mobile_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_cid)['communication_mobile'])
      && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_cid)['communication_mobile']);
    $par_data_person->set('communication_mobile', $mobile_phone_preference_value);

    // Set the data for the legal entities.
    $legal_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_add_legal_entity');
    $legal_entities = $this->getFlowDataHandler()->getTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'legal_entity', $legal_cid) ?: [];
    $par_data_legal_entities = [];
    foreach ($this->getFlowDataHandler()->getTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'legal_entity', $legal_cid) as $delta => $legal_entity) {
      // These ones need to be saved fresh.
      $par_data_legal_entities[$delta] = ParDataLegalEntity::create([
        'registered_name' => $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'legal_entity', $delta, 'registered_name'], $legal_cid),
        'registered_number' => $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'legal_entity', $delta, 'registered_number'], $legal_cid),
        'legal_entity_type' => $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'legal_entity', $delta, 'legal_entity_type'], $legal_cid),
      ]);
    }

    $existing_legal_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_select_legal_entities');
    $existing_legal_entities = $this->getFlowDataHandler()->getTempDataValue('field_legal_entity', $existing_legal_cid) ?: [];
    $par_data_legal_entities_existing = [];
    foreach ($existing_legal_entities as $delta => $existing_legal_entity) {
      if ($existing = ParDataLegalEntity::load($existing_legal_entity)) {
        $par_data_legal_entities_existing[$delta] = ParDataLegalEntity::load($existing_legal_entity);
      }
    }

    // Save the data for the SIC code form.
    $sic_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_sic_code');
    $par_data_organisation->get('field_sic_code')->set(0, $this->getFlowDataHandler()->getTempDataValue('sic_code', $sic_cid));

    if ($par_data_partnership->isDirect()) {
      $employee_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_employee_number');
      $par_data_organisation->set('employees_band', $this->getFlowDataHandler()->getTempDataValue('employees_band', $employee_cid));
    }
    if ($par_data_partnership->isCoordinated()) {
      // Save the data for the business size form.
      $business_size_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_business_size');
      $par_data_organisation->set('size', $this->getFlowDataHandler()->getTempDataValue('business_size', $business_size_cid));
    }

    // Save the data for the trading name form.
    $trading_cid = $this->getFlowNegotiator()->getFormKey('par_partnership_confirmation_trading_name');
    $par_data_organisation->set('trading_name', $this->getFlowDataHandler()->getTempDataValue('trading_name', $trading_cid));

    return [
      'par_data_partnership' => $par_data_partnership,
      'par_data_organisation' => $par_data_organisation,
      'par_data_person' => $par_data_person,
      'par_data_premises' => $par_data_premises,
      'par_data_legal_entities' => $par_data_legal_entities,
      'par_data_legal_entities_existing' => $par_data_legal_entities_existing,
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
    /** @var ParDataPerson $par_data_person */
    /** @var ParDataPremises $par_data_premises */
    /** @var ParDataLegalEntity[] $par_data_legal_entities */
    /** @var ParDataLegalEntity[] $par_data_legal_entities_existing */

    // Add all references if not already set.
    if ($par_data_person->save() && !$par_data_partnership->getOrganisationPeople(TRUE)) {
      $par_data_partnership->get('field_organisation_person')->set(0, $par_data_person);
      $par_data_organisation->get('field_person')->appendItem($par_data_person);
    }
    // Save the new legal entities.
    foreach ($par_data_legal_entities_existing + $par_data_legal_entities as $key => $legal_entity) {
      // Save the new legal entities and add to the organisation.
      if ($legal_entity->isNew()) {
        $legal_entity->save();
        $par_data_organisation->get('field_legal_entity')->appendItem($legal_entity);
      }
      $par_data_partnership->get('field_legal_entity')->appendItem($legal_entity);
    }
    if ($par_data_premises->save() && !$par_data_organisation->getPremises(TRUE)) {
      $par_data_organisation->get('field_premises')->set(0, $par_data_premises);
    }
    if ($par_data_organisation->save() && !$par_data_partnership->getOrganisation(TRUE)) {
      $par_data_partnership->get('field_organisation')->set(0, $par_data_organisation);
    }

    // Set the confirmed by business field.
    if ($par_data_partnership && !$par_data_partnership->getBoolean('partnership_info_agreed_business')) {
      // Save the value for the confirmation field.
      $par_data_partnership->set('partnership_info_agreed_business', $this->decideBooleanValue($this->getFlowDataHandler()->getTempDataValue('partnership_info_agreed_business')));
      $par_data_partnership->set('terms_organisation_agreed', $this->decideBooleanValue($this->getFlowDataHandler()->getTempDataValue('terms_organisation_agreed')));

      // Set partnership status.
      try {
        $par_data_partnership->setParStatus('confirmed_business');
      }
      catch (ParDataException $e) {
        // If the status could not be updated we want to log this but continue.
        $message = $this->t("This status could not be updated to 'Approved by the Organisation' for the %label");
        $replacements = [
          '%label' => $par_data_partnership->label(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }

    // Save the partnership.
    if ($par_data_partnership->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('This %confirm could not be saved for %form_id');
      $replacements = [
        '%confirm' => $par_data_partnership->get('partnership_info_agreed_business')->toString(),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);

      // If the partnership could not be saved the application can't be progressed.
      // @TODO Find a better way to alert the user without redirecting them away from the form.
      drupal_set_message('There was an error progressing your partnership, please contact the helpdesk for more information.');
      $form_state->setRedirect($this->getFlowNegotiator()->getFlow()->getPrevRoute('cancel'));
    }

  }

}
