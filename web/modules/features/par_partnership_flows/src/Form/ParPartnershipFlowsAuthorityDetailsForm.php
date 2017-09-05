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

      // Partnership Information Confirmation.
      $confirmation_value = $par_data_partnership->getBoolean('partnership_info_agreed_business');
      $this->loadDataValue('confirmation', $confirmation_value);
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

    $form['details_intro'] = [
      '#markup' => "Review and confirm the details of your partnership with " . $par_data_authority->authority_name->getString(),
    ];

    $form['details_intro'] = [
      '#type' => 'markup',
      '#markup' => t('Primary authority information for:'),
    ];

    $business_name = $organisation_builder->view($par_data_organisation, 'title');
    $business_name['#prefix'] = '<h1>';
    $business_name['#suffix'] = '</h1>';
    $form['business_name'] = $this->renderMarkupField($business_name);

    // Registered address.
    $par_data_premises = $par_data_organisation->getPremises();
    $registered_premises = array_shift($par_data_premises);

    if ($registered_premises) {
      $premises_view_builder = $this->getParDataManager()->getViewBuilder('par_data_premises');

      $form['registered_address'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $registered_address = $premises_view_builder->view($registered_premises, 'summary');
      $form['registered_address']['address'] = $this->renderMarkupField($registered_address);
    }

    // About the business.
    $about_organisation = $organisation_builder->view($par_data_organisation, 'about');

    $form['about_business'] = [
      '#type' => 'fieldset',
      '#title' => t('About the business:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['about_business']['info'] = $this->renderMarkupField($about_organisation);

    // Sic Codes.
    $par_data_sic_code = $par_data_organisation->getSicCode();
    $form['sic_codes'] = [
      '#type' => 'fieldset',
      '#title' => t('SIC Code:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    // Check to see if there are any sic codes to be shown.
    if ($par_data_sic_code) {
      foreach ($par_data_sic_code as $sic_code) {
        $sic_code_view_builder = $this->getParDataManager()->getViewBuilder('par_data_sic_code');
        // @todo need to put these on one line.
        $sic_code_item = $sic_code_view_builder->view($sic_code, 'full');
        $form['sic_codes'][$sic_code->id()] = $this->renderMarkupField($sic_code_item);
      }
    }
    else {
      $form['sic_codes']['none'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
      ];
    }

    // Number of employees.
    $form['employee_no'] = [
      '#type' => 'fieldset',
      '#title' => t('Number of Employees:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    if ($par_data_organisation->get('employees_band')->getString() !== '0') {
      $form['employee_no']['item'] = [
        '#type' => 'markup',
        '#markup' => $par_data_organisation->get('employees_band')->getString(),
      ];
    }
    else {
      $form['employee_no']['item'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
      ];
    }

    // Legal Entities.
    $par_data_legal_entities = $par_data_organisation->getLegalEntity();
    $par_data_legal_entity = array_shift($par_data_legal_entities);
    $form['legal_entity'] = [
      '#type' => 'fieldset',
      '#title' => t('Legal Entities:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    if ($par_data_legal_entity) {

      $legal_entity_view_builder = $this->getParDataManager()->getViewBuilder('par_data_legal_entity');
      $legal_entity = $legal_entity_view_builder->view($par_data_legal_entity, 'full');
      $form['legal_entity']['entity'] = $this->renderMarkupField($legal_entity);

    }

    if ($par_data_legal_entities) {

      foreach ($par_data_legal_entities as $legal_entity_item) {
        $form['legal_entity_' . $legal_entity_item->id()] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];
        $alternative_legal = $legal_entity_view_builder->view($legal_entity_item, 'full');
        $form['legal_entity_' . $legal_entity_item->id()]['item'] = $this->renderMarkupField($alternative_legal);

      }
    }

    // Trading names.
    $par_data_trading_names = $par_data_organisation->get('trading_name')->getValue();
    if ($par_data_trading_names) {
      $form['trading_names'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
      ];

      foreach ($par_data_trading_names as $key => $trading_name) {
        $form['trading_names'][$key] = [
          '#type' => 'fieldset',
          '#title' => $key === 0 ? t('Trading Names:') : '',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $form['trading_names'][$key]['entity'] = [
          '#type' => 'markup',
          '#markup' => $trading_name['value'],
          '#prefix' => '<div>',
          '#suffix' => '</div>',
        ];

      }

    }

    $par_data_authority = current($par_data_partnership->getAuthority());
    $form['authority'] = [
      '#type' => 'markup',
      '#markup' => $par_data_authority->get('authority_name')->getString(),
      '#prefix' => '<h1>',
      '#suffix' => '</h1>',
    ];

    $form['partnership_since'] = [
      '#type' => 'fieldset',
      '#title' => t('In partnership since:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['partnership_since']['approved_date'] = [
      '#type' => 'markup',
      '#markup' => $par_data_partnership->get('approved_date')->getString(),
    ];

    $form['partnered'] = [
      '#type' => 'fieldset',
      '#title' => t('Partnered for:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $regulatory_functions = $par_data_partnership->getRegulatoryFunction();
    foreach ($regulatory_functions as $regulatory_function) {
      $functions[] = $regulatory_function->get('function_name')->getString();
    }
    $all_functions = implode(', ', $functions);

    $form['partnered']['functions'] = [
      '#type' => 'markup',
      '#markup' => $all_functions,
    ];

    // Check to see if there are additional addresses to be shown.
    if ($par_data_premises) {
      $form['alternate_address'] = [
        '#type' => 'fieldset',
        '#title' => t('Additional Premises:'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      foreach ($par_data_premises as $premises) {
        $person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_premises');

        $alternative_person = $person_view_builder->view($premises, 'summary');
        $form['alternate_address'][$premises->id()]['premises'] = $this->renderMarkupField($alternative_person);

      }
    }

    // About the Partnership.
    $form['about_partnership'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#title' => t('About the partnership:'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $partnership_view_builder = $this->getParDataManager()->getViewBuilder('par_data_partnership');

    $form['about_partnership']['details'] = $par_data_partnership ? $partnership_view_builder->view($par_data_partnership, 'about') : '';

    // Go to the second step.
    $form['about_partnership']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('about')->setText('edit')->toString(),
      ]),
    ];

    $form['inspection_plans'] = [
      '#type' => 'fieldset',
      '#title' => t('Inspection plans:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['inspection_plans']['edit'] = [
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
    $form['advice']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('advice')->setText('See all Advice')->toString(),
      ]),
    ];

    // Contacts.
    // Local Authority.
    $par_data_contacts = $par_data_partnership->getAuthorityPeople();
    $form['authority_contact'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#title' => t('Contacts - Primary Authority:'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    if ($par_data_contacts) {

      foreach ($par_data_contacts as $person) {
        $person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');

        $alternative_person = $person_view_builder->view($person, 'summary');

        $form['authority_contact'][$person->id()] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $form['authority_contact'][$person->id()]['person'] = $this->renderMarkupField($alternative_person);

        // We can get a link to a given form step like so.
        $form['authority_contact'][$person->id()]['edit'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlow()->getNextLink('edit_contact', [
              'par_data_person' => $person->id(),
            ])->setText('edit')->toString(),
          ]),
        ];
      }
    }
    else {
      $form['authority_contact']['details'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
      ];

    }

    // Primary contact summary.
    $par_data_contacts = $par_data_partnership->getOrganisationPeople();

    $form['organisation_contact'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#title' => t('Contacts - Organisation:'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    if ($par_data_contacts) {

      foreach ($par_data_contacts as $person) {
        $person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');

        $alternative_person = $person_view_builder->view($person, 'summary');

        $form['organisation_contact'][$person->id()] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $form['organisation_contact'][$person->id()]['person'] = $this->renderMarkupField($alternative_person);
      }
    }
    else {
      $form['organisation_contact']['details'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
      ];

    }

    $form['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => $this->t('Save'),
    ];

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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
