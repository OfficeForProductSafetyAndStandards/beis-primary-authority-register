<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the partnership details.
 */
class ParPartnershipFlowsDetailsForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_details';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $par_data_partnership = $this->getRouteParam('par_data_partnership');
    if ($par_data_partnership) {
      $par_data_organisation = current($par_data_partnership->getOrganisation());
      return $par_data_organisation->get('organisation_name')->getString();
    }

    return parent::titleCallback();
  }

  /**
   * Helper to get all the editable values.
   *
   * Used for when editing or revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Authority being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      $this->loadDataValue("partnership_info_agreed_authority", $par_data_partnership->get('partnership_info_agreed_authority')->getString());
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);
    // Configuration for each entity is contained within the bundle.
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    // Display all the information that can be modified by the organisation.
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $organisation_builder = $this->getParDataManager()->getViewBuilder('par_data_organisation');

    // Display the primary address along with the link to edit it.
    $form['registered_address'] = $this->renderSection('Registered address', $par_data_organisation, ['field_premises' => 'summary'], ['edit-entity'], FALSE, TRUE);

    // View and perform operations on the information about the business.
    $form['about_business'] = $this->renderSection('About the business', $par_data_organisation, ['comments' => 'about'], ['edit-field']);

    // Only show SIC Codes and Employee number if the partnership is a direct partnership.
    if ($par_data_partnership->isDirect()) {
      // Add the SIC Codes with the relevant operational links.
      $form['sic_codes'] = $this->renderSection('SIC Codes', $par_data_organisation, ['field_sic_code' => 'full'], ['edit-field', 'add']);

      // Add the number of employees with a link to edit the field.
      $form['employee_no'] = $this->renderSection('Number of Employees', $par_data_organisation, ['employees_band' => 'full'], ['edit-field']);
    }

    // Only show Members list, Sectors and Number of businesses if the partnership is a coordinated partnership.
    if ($par_data_partnership->isCoordinated()) {
      $form['associations'] = $this->renderSection('Number of members', $par_data_organisation, ['size' => 'full'], ['edit-field']);

      // Display all the legal entities along with the links for the allowed operations on these.
      $form['members'] = $this->renderSection('Members', $par_data_partnership, ['field_coordinated_business' => 'title']);
    }

    // Display all the legal entities along with the links for the allowed operations on these.
    $form['legal_entities'] = $this->renderSection('Legal Entities', $par_data_organisation, ['field_legal_entity' => 'summary'], ['edit-entity', 'add']);

    // Display all the trading names along with the links for the allowed operations on these.
    $form['trading_names'] = $this->renderSection('Trading Names', $par_data_organisation, ['trading_name' => 'full'], ['edit-field', 'add']);

    // Everything below is for the authorioty to edit and add to.
    $par_data_authority = current($par_data_partnership->getAuthority());
    $form['authority'] = [
      '#type' => 'markup',
      '#markup' => $par_data_authority->get('authority_name')->getString(),
      '#prefix' => '<h1>',
      '#suffix' => '</h1>',
    ];

    // Display details about the partnership for information.
    $form['partnership_since'] = $this->renderSection('In partnership since', $par_data_partnership, ['approved_date' => 'full']);

    // Display details about the partnership for information.
    $form['regulatory_functions'] = $this->renderSection('Partnered for', $par_data_partnership, ['field_regulatory_function' => 'full']);

    // Display details about the partnership for information.
    $form['about_partnership'] = $this->renderSection('About the partnership', $par_data_partnership, ['about_partnership' => 'about'], ['edit-field']);

    $form['inspection_plans'] = [
      '#type' => 'fieldset',
      '#title' => t('Inspection plans'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['inspection_plans']['link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()
          ->getNextLink('inspection_plans')
          ->setText('See all Inspection Plans')
          ->toString(),
      ]),
    ];

    $form['advice'] = [
      '#type' => 'fieldset',
      '#title' => t('Advice and Documents'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['advice']['link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()
          ->getNextLink('advice')
          ->setText('See all Advice')
          ->toString(),
      ]),
    ];

    // Display the authority contacts for information.
    $form['authority_contacts'] = $this->renderSection('Contacts - Primary Authority', $par_data_partnership, ['field_authority_person' => 'detailed'], ['edit-entity', 'add']);

    // Display all the legal entities along with the links for the allowed
    // operations on these.
    $form['organisation_contacts'] = $this->renderSection('Contacts - Organisation', $par_data_partnership, ['field_organisation_person' => 'detailed'], ['edit-entity', 'add']);

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($partnership_bundle);
    $this->addCacheableDependency($person_bundle);
    $this->addCacheableDependency($legal_entity_bundle);
    $this->addCacheableDependency($premises_bundle);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Make sure the confirm box is ticked.
    if (!$form_state->getValue('partnership_info_agreed_authority')) {
      $this->setElementError('partnership_info_agreed_authority', $form_state, 'Please confirm you have reviewed the details.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getRouteParam('par_data_partnership');
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $par_data_person = current($par_data_organisation->getPerson());

    if ($par_data_partnership && !$par_data_partnership->get('partnership_info_agreed_authority')->getString()) {

      // Save the value for the confirmation field.
      $par_data_partnership->set('partnership_info_agreed_authority', $this->getTempDataValue('partnership_info_agreed_authority'));

      if ($par_data_partnership->save()) {
        $this->deleteStore();
      }
      else {
        $message = $this->t('This %confirm could not be saved for %form_id');
        $replacements = [
          '%confirm' => $par_data_partnership->get('partnership_info_agreed_authority')->toString(),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }
  }

}
