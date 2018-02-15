<?php

namespace Drupal\par_member_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_member_add_flows\ParFlowAccessTrait;

/**
 * The form for the partnership details.
 */
class ParConfirmationReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_member_add_review';
  }

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Member summary';

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
    /** @var ParDataCoordinatedBusiness $par_data_coordinated_business */
    /** @var ParDataOrganisation $par_data_organisation */
    /** @var ParDataPerson $par_data_person */
    /** @var ParDataPremises $par_data_premises */
    /** @var ParDataLegalEntity[] $par_data_legal_entities */

    // Display organisation name.
    $form['organisation_name'] = $this->renderSection('Member business name', $par_data_organisation, ['organisation_name' => 'title']);

    // Display the member's address
    $form['member_registered_address'] = $this->renderSection('Member business address', $par_data_premises, ['address' => 'summary']);

    // Display contacts at the organisation.
    $form['member_contact'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#title' => 'Member contact details',
      'person' => $this->renderEntities('Legal entities', [$par_data_person], 'detailed'),
    ];

    // Display legal entities.
    $form['legal_entities'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#title' => 'Legal Entities',
      'legal_entities' => $this->renderEntities('Legal entities', $par_data_legal_entities),
    ];

    // Display the trading names.
    $form['trading_names'] = $this->renderSection('Trading names', $par_data_organisation, ['trading_name' => 'title']);

    // Display whether this is covered by an inspeciton plan.
    $form['covered_by_inspection'] = $this->renderSection('Covered by inspection plan', $par_data_coordinated_business, ['covered_by_inspection' => 'default']);

    return parent::buildForm($form, $form_state);
  }

  public function createEntities() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Get the organisation name form details.
    $organisation_name_cid = $this->getFlowNegotiator()->getFormKey('par_member_add_organisation_name');
    $organisation_name = $this->getFlowDataHandler()->getTempDataValue('organisation_name', $organisation_name_cid);

    // Get the trading name data.
    $trading_name_cid = $this->getFlowNegotiator()->getFormKey('par_member_add_trading_name');
    $trading_name_plugins = $this->getFlowDataHandler()->getTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'trading_name', $trading_name_cid) ?: [];
    $trading_names = [];
    foreach ($trading_name_plugins as $delta => $trading_name) {
      $trading_names[$delta] = $trading_name['trading_name'];
    }

    // Get the covered by inspection plan data.
    $membership_start_cid = $this->getFlowNegotiator()->getFormKey('par_member_add_begin_date');

    // Get the covered by inspection plan data.
    $covered_by_cid = $this->getFlowNegotiator()->getFormKey('par_member_add_inspection_plan_coverage');
    $covered_by_inspection_plan = $this->decideBooleanValue($this->getFlowDataHandler()->getTempDataValue('covered_by_inspection', $covered_by_cid), "1", "0");

    // Get the address data.
    $address_cid = $this->getFlowNegotiator()->getFormKey('par_member_add_address');
    $address = [
      'country_code' => $this->getFlowDataHandler()->getTempDataValue('country_code', $address_cid),
      'address_line1' => $this->getFlowDataHandler()->getTempDataValue('address_line1', $address_cid),
      'address_line2' => $this->getFlowDataHandler()->getTempDataValue('address_line2', $address_cid),
      'locality' => $this->getFlowDataHandler()->getTempDataValue('town_city', $address_cid),
      'administrative_area' => $this->getFlowDataHandler()->getTempDataValue('county', $address_cid),
      'postal_code' => $this->getFlowDataHandler()->getTempDataValue('postcode', $address_cid),
    ];
    $nation = $this->getFlowDataHandler()->getTempDataValue('country', $address_cid);

    // Get the contact details data.
    $contact_cid = $this->getFlowNegotiator()->getFormKey('par_member_add_contact');
    $person = [
      'salutation' => $this->getFlowDataHandler()->getTempDataValue('salutation', $contact_cid),
      'first_name' => $this->getFlowDataHandler()->getTempDataValue('first_name', $contact_cid),
      'last_name' => $this->getFlowDataHandler()->getTempDataValue('last_name', $contact_cid),
      'work_phone' => $this->getFlowDataHandler()->getTempDataValue('work_phone', $contact_cid),
      'mobile_phone' => $this->getFlowDataHandler()->getTempDataValue('mobile_phone', $contact_cid),
      'email' => $this->getFlowDataHandler()->getTempDataValue('email', $contact_cid),
    ];

    // Get the legal entity data.
    $legal_cid = $this->getFlowNegotiator()->getFormKey('par_member_add_legal_entity');
    $legal_entities = $this->getFlowDataHandler()->getTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'legal_entity', $legal_cid) ?: [];
    $par_data_legal_entities = [];
    foreach ($legal_entities as $delta => $legal_entity) {
      // These ones need to be saved fresh.
      $par_data_legal_entities[$delta] = ParDataLegalEntity::create([
        'registered_name' => $legal_entity['registered_name'],
        'registered_number' => $legal_entity['registered_number'],
        'legal_entity_type' => $legal_entity['legal_entity_type'],
      ]);
    }

    // Create the entities.
    $par_data_coordinated_business = ParDataCoordinatedBusiness::create();
    $par_data_coordinated_business->get('covered_by_inspection')->setValue($covered_by_inspection_plan);
    $par_data_organisation = ParDataOrganisation::create([
      'organisation_name' => $organisation_name,
      'trading_name' => $trading_names,
    ]);
    $par_data_premises = ParDataPremises::create([
      'address' => $address,
    ]);
    if (!empty($nation)) {
      $par_data_premises->set('nation', $nation);
    }
    $par_data_person = ParDataPerson::create($person);

    return [
      'par_data_partnership' => $par_data_partnership,
      'par_data_coordinated_business' => $par_data_coordinated_business,
      'par_data_organisation' => $par_data_organisation,
      'par_data_person' => $par_data_person,
      'par_data_premises' => $par_data_premises,
      'par_data_legal_entities' => $par_data_legal_entities,
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


    // @TODO Save all the new data.

  }

}
