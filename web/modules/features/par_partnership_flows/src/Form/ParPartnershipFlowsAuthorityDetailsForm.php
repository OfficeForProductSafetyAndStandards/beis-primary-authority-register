<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the partnership details.
 */
class ParPartnershipFlowsAuthorityDetailsForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_authority_details';
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

    $par_data_authority = current($par_data_partnership->getAuthority());

    // Organisation summary.
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $organisation_builder = $this->getParDataManager()->getViewBuilder('par_data_organisation');


    // Display the primary address along with the link to edit it.
    $form['registered_address'] = $this->renderSection('Registered address', $par_data_organisation, ['field_premises' => 'summary'], [], FALSE, TRUE);

    // View and perform operations on the information about the business.
    $form['about_business'] = $this->renderSection('About the business', $par_data_organisation, ['comments' => 'about']);

    // Only show SIC Codes and Employee number if the partnership is a direct partnership.
    if ($par_data_partnership->isDirect()) {
      // Add the SIC Codes with the relevant operational links.
      $form['sic_codes'] = $this->renderSection('SIC Codes', $par_data_organisation, ['field_sic_code' => 'full']);

      // Add the number of employees with a link to edit the field.
      $form['employee_no'] = $this->renderSection('Number of Employees', $par_data_organisation, ['employees_band' => 'full']);
    }

    // Only show Members list, Sectors and Number of businesses if the partnership is a coordinated partnership.
    if ($par_data_partnership->isCoordinated()) {
      $form['associations'] = $this->renderSection('Number of Associations', $par_data_organisation, ['size' => 'full']);

      // @TODO We need to add members list and Sectors. TBD Later.
    }

    // Display all the legal entities along with the links for the allowed operations on these.
    $form['legal_entities'] = $this->renderSection('Legal Entities', $par_data_organisation, ['field_legal_entity' => 'summary']);

    // Display all the trading names along with the links for the allowed operations on these.
    $form['trading_names'] = $this->renderSection('Trading Names', $par_data_organisation, ['trading_name' => 'full']);

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
      '#title' => t('Inspection plans:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['inspection_plans']['link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('inspection_plans')->setText('See all Inspection Plans')->toString(),
      ]),
    ];

    $form['advice'] = [
      '#type' => 'fieldset',
      '#title' => t('Advice and Documents:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['advice']['link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('advice')->setText('See all Advice')->toString(),
      ]),
    ];

    // Display the authority contacts for information.
    $form['authority_contacts'] = $this->renderSection('Contacts - organisation', $par_data_partnership, ['field_authority_person' => 'summary'], ['edit-entity', 'add']);

    // Display all the legal entities along with the links for the allowed operations on these.
    $form['organisation_contacts'] = $this->renderSection('Contacts - organisation', $par_data_partnership, ['field_organisation_person' => 'summary']);

    $form['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => $this->t('Done'),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($partnership_bundle);
    $this->addCacheableDependency($person_bundle);
    $this->addCacheableDependency($legal_entity_bundle);
    $this->addCacheableDependency($premises_bundle);

    return parent::buildForm($form, $form_state);
  }

}
