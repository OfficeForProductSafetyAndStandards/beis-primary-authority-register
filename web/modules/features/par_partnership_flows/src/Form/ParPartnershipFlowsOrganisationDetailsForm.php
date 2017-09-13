<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowException;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The organisation details form for the corrdinator and direct journey.
 */
class ParPartnershipFlowsOrganisationDetailsForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_organisation_details';
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

    // Display all the information that can be modified by the organisation.
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $organisation_builder = $this->getParDataManager()->getViewBuilder('par_data_organisation');

    $string = $par_data_partnership->isDirect() ? 'direct' : 'coordinated';
    $form['type'] = [
      '#type' => 'markup',
      '#markup' => $string,
    ];

    $business_name = $organisation_builder->view($par_data_organisation, 'title');
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
      $form['registered_address']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlow()->getNextLink('edit_address', [
            'par_data_premises' => $registered_premises->id(),
          ])->setText('edit')->toString(),
        ]),
      ];
    }

    // View and perform operations on the information about the business.
    $about_organisation = $organisation_builder->view($par_data_organisation, 'about');
    $form['about_business'] = [
      '#type' => 'fieldset',
      '#title' => t('About the business:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['about_business']['info'] = $this->renderMarkupField($about_organisation);
    $form['about_business']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('about')->setText('edit')->toString(),
      ]),
    ];

    // Only show SIC Codes and Employee number if the partnership is a direct partnership.
    if ($par_data_partnership->isDirect()) {
      // Add the SIC Codes with the relevant operational links.
      $form['sic_codes'] = $this->renderSection('SIC Codes', $par_data_organisation, ['field_sic_code' => 'full'], ['edit-field', 'add']);

      // Number of employees.
      $form['employee_no'] = [
        '#type' => 'fieldset',
        '#title' => t('Number of Employees:'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      if (!$par_data_organisation->get('employees_band')->isEmpty()) {
        $form['employee_no']['item'] = [
          '#type' => 'markup',
          '#markup' => $par_data_organisation->get('employees_band')->getString(),
        ];
      } else {
        $form['employee_no']['item'] = [
          '#type' => 'markup',
          '#markup' => $this->t('(none)'),
        ];
      }
      $form['employee_no']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlow()->getNextLink('size')->setText('edit')->toString(),
        ]),
      ];
    }

    // Only show Members list, Sectors and Number of businesses if the partnership is a coordinated partnership.
    if ($par_data_partnership->isCoordinated()) {
      // @TODO We have none of these fields yet and have no way to store them.
    }

    // Display all the legal entities along with the links for the allowed operations on these.
    $form['legal_entities'] = $this->renderSection('Legal Entities', $par_data_organisation, ['field_legal_entity' => 'summary'], ['edit-entity', 'add']);

    // Display all the trading names along with the links for the allowed operations on these.
    $par_data_trading_names = $par_data_organisation->get('trading_name')->getValue();
    $form['trading_names'] = [
      '#type' => 'fieldset',
      '#title' => t('Trading Names:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    if ($par_data_trading_names) {
      foreach ($par_data_trading_names as $key => $trading_name) {
        $form['trading_names'][$key] = [
          '#type' => 'fieldset',
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

        $form['trading_names'][$key]['edit'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlow()->getNextLink('edit_trading', [
              'trading_name_delta' => $key,
            ])->setText('edit')->toString(),
          ]),
        ];

      }
    }
    else {
      $form['trading_names']['item'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
      ];
    }

    $form['trading_names_add'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['trading_names_add']['add'] = [
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('add_trading')->setText('add another')->toString(),
      ]),
    ];

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

    if (!empty($functions)) {
      $all_functions = implode(', ', $functions);

      $form['partnered']['functions'] = [
        '#type' => 'markup',
        '#markup' => $all_functions,
      ];
    }
    else {
      $form['partnered']['functions'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
      ];
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

    if ($par_data_partnership->isDirect()) {
      $form['sic_code'] = [
        '#type' => 'fieldset',
        '#title' => t('SIC Code:'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $sic_codes = $par_data_organisation->getSicCode();

      foreach ($sic_codes as $key => $sic_code) {
        if ($id = $sic_code->id()) {

          $sic_code_view_builder = $this->getParDataManager()
            ->getViewBuilder('par_data_sic_code');
          $sic_code_item = $sic_code_view_builder->view($sic_code, 'full');
          $form['sic_code'][$id]['item'] = $this->renderMarkupField($sic_code_item);

          $form['sic_code'][$id]['edit'] = [
            '#type' => 'markup',
            '#markup' => t('@link', [
              '@link' => $this->getFlow()
                ->getNextLink('edit_sic', ['sic_code_delta' => $key])
                ->setText('edit')
                ->toString(),
            ]),
          ];
        }
      }
    }

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

        $alternative_person = $person_view_builder->view($person, 'detailed');

        $form['authority_contact'][$person->id()] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $form['authority_contact'][$person->id()]['person'] = $this->renderMarkupField($alternative_person);
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

        $alternative_person = $person_view_builder->view($person, 'detailed');

        $form['organisation_contact'][$person->id()] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $form['organisation_contact'][$person->id()]['person'] = $this->renderMarkupField($alternative_person);
        $form['organisation_contact'][$person->id()]['edit'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlow()->getNextLink('edit_contact', [
              'par_data_person' => $person->id(),
            ])->setText('edit')->toString(),
          ]),
        ];
        $form['organisation_contact'][$person->id()]['delete'] = [
          '#type' => 'markup',
          '#markup' => t('<a href="#">remove (TBC)</a>'),
        ];
      }
    }
    else {
      $form['organisation_contact']['details'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
      ];

    }

    $form['organisation_contact_add'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['organisation_contact_add']['add'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()
          ->getNextLink('add_contact')
          ->setText('add another contact (TBC)')
          ->toString(),
      ]),
    ];

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
