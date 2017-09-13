<?php

namespace Drupal\par_enforcement_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for rendering a specific partner page.
 */
class ParEnforcementFlowsPartnershipPageController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'search_enforcement';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {

    // Configuration for each entity is contained within the bundle.
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');
    $par_data_authority = current($par_data_partnership->getAuthority());

    // Organisation summary.
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $organisation_builder = $this->getParDataManager()->getViewBuilder('par_data_organisation');

    $build['details_intro'] = [
      '#type' => 'markup',
      '#markup' => t('Primary authority information for:'),
    ];



    $business_name = $organisation_builder->view($par_data_organisation, 'title');
    $business_name['#prefix'] = '<h1>';
    $business_name['#suffix'] = '</h1>';
    $build['business_name'] = $this->renderMarkupField($business_name);


    // Registered address.
    $par_data_premises = $par_data_organisation->getPremises();
    $registered_premises = array_shift($par_data_premises);

    if ($registered_premises) {
      $premises_view_builder = $this->getParDataManager()->getViewBuilder('par_data_premises');
      $registered_address = $premises_view_builder->view($registered_premises, 'summary');
      $build['registered_address']['address'] = $this->renderMarkupField($registered_address);
    }

    // About the business.

    $build['about_business_title'] = [
      '#type' => 'markup',
      '#markup' => t('About the business:'),
      '#prefix' => '<b>',
      '#suffix' => '</b></br>',

    ];

    $about_organisation = $organisation_builder->view($par_data_organisation, 'about');
    $build['about_business']['info'] = $this->renderMarkupField($about_organisation);
    $build['about_business']['info']['#suffix'] = '</br></br>';

    $build['business_message'] = [
      '#type' => 'markup',
      '#markup' => t('Send a message about this business'),
      '#suffix' => '</br>'
    ];

    $build['business_message link'] = [
      '#type' => 'markup',
      '#markup' => t('<a href="/enforcement-notice/raise">Send notification of enforcement action</a>'),
      '#suffix' => '</br></br>'
    ];

    // Sic Codes.
    $par_data_sic_code = $par_data_organisation->getSicCode();

    $build['sic_codes_title'] = [
      '#type' => 'markup',
      '#markup' => t('SIC Code'),
      '#prefix' => '<h3><b>',
      '#suffix' => '</b></h3>',
    ];

    // Check to see if there are any sic codes to be shown.
    if ($par_data_sic_code) {
      foreach ($par_data_sic_code as $sic_code) {
        $sic_code_view_builder = $this->getParDataManager()->getViewBuilder('par_data_sic_code');
        // @todo need to put these on one line.
        $sic_code_item = $sic_code_view_builder->view($sic_code, 'full');
        $build['sic_codes'][$sic_code->id()] = $this->renderMarkupField($sic_code_item);
        $build['sic_codes'][$sic_code->id()]['#suffix'] = '</br>';
      }
    }
    else {
      $build['sic_codes']['none'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
        '#suffix' => '</br>',
      ];
    }

    // Number of employees.
    $build['employee_no'] = [
      '#type' => 'markup',
      '#markup' => t('Number of Employees:'),
      '#prefix' => '<h3><b>',
      '#suffix' => '</b></h3>',
    ];

    if ($par_data_organisation->get('employees_band')->getString() !== '0') {
      $build['employee_no_item'] = [
        '#type' => 'markup',
        '#markup' => $par_data_organisation->get('employees_band')->getString(),
        '#suffix' => '</br>',
      ];
    }
    else {
      $build['employee_no_item'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)') . '</br>',
        '#suffix' => '</br>',
      ];
    }

    // Legal Entities.
    $par_data_legal_entities = $par_data_organisation->getLegalEntity();
    $par_data_legal_entity = array_shift($par_data_legal_entities);

    $build['legal_entity'] = [
      '#type' => 'markup',
      '#markup' =>  '<h3><b>' . t('Legal Entities:') . '</b></h3>',
    ];

    if ($par_data_legal_entity) {
      $legal_entity_view_builder = $this->getParDataManager()->getViewBuilder('par_data_legal_entity');
      $legal_entity = $legal_entity_view_builder->view($par_data_legal_entity, 'full');
      $build['legal_entity']['entity'] = $this->renderMarkupField($legal_entity);
      $build['legal_entity']['entity']['#suffix'] = '</br>';

    }

    if ($par_data_legal_entities) {

      foreach ($par_data_legal_entities as $legal_entity_item) {
        $alternative_legal = $legal_entity_view_builder->view($legal_entity_item, 'full');
        $build['legal_entity_' . $legal_entity_item->id()]['item'] = $this->renderMarkupField($alternative_legal);
        $build['legal_entity_' . $legal_entity_item->id()]['item']['#suffix'] = '</br>';

      }
    }


    // Trading names.
    $par_data_trading_names = $par_data_organisation->get('trading_name')->getValue();
    if ($par_data_trading_names) {

      foreach ($par_data_trading_names as $key => $trading_name) {

        $build['trading_names'][$key] = [
          '#type' => 'markup',
          '#markup' =>  $key === 0 ?  '<h3><b>' . t('Trading Names:') . '</b></h3>' : '',
        ];

        $build['trading_names'][$key]['entity'] = [
          '#type' => 'markup',
          '#markup' => $trading_name['value'],
          '#prefix' => '<div>',
          '#suffix' => '</div></br>',
        ];
      }

    }

    $par_data_authority = current($par_data_partnership->getAuthority());
    $build['authority'] = [
      '#type' => 'markup',
      '#markup' => $par_data_authority->get('authority_name')->getString(),
      '#prefix' => '<h1>',
      '#suffix' => '</h1></br>',
    ];


    $build['partnership_since'] = [
      '#type' => 'markup',
      '#markup' =>  t('In partnership since:'),
      '#prefix' => '<h3>',
      '#suffix' => '</h3></br>',
    ];


    $build['partnership_since_date'] = [
      '#type' => 'markup',
      '#markup' => $par_data_partnership->get('approved_date')->getString(),
      '#suffix' => '</br>',
    ];


    $build['partnered'] = [
      '#type' => 'markup',
      '#markup' =>  t('Partnered for:'),
      '#prefix' => '<h3>',
      '#suffix' => '</h3></br>',
    ];

    $regulatory_functions = $par_data_partnership->getRegulatoryFunction();
    foreach ($regulatory_functions as $regulatory_function) {
      $functions[] = $regulatory_function->get('function_name')->getString();
    }

    if (!empty($functions)) {
      $all_functions = implode(', ', $functions);

      $build['partnered_functions'] = [
        '#type' => 'markup',
        '#markup' => $all_functions,
        '#prefix' => '<div>',
        '#suffix' => '</div></br>',
      ];
    }
    else {
      $build['partnered_functions'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
        '#suffix' => '</br>',
      ];
    }

    // About the Partnership.
    $build['about_partnership'] = [
      '#type' => 'markup',
      '#markup' => t('About the partnership:'),
      '#prefix' => '<h3>',
      '#suffix' => '</h3></br>',
    ];

    $partnership_view_builder = $this->getParDataManager()->getViewBuilder('par_data_partnership');

    $build['about_partnership_details'] = $par_data_partnership ? $partnership_view_builder->view($par_data_partnership, 'about') : '';


    $build['inspection_plans'] = [
      '#type' => 'markup',
      '#markup' =>  t('Inspection plans:'),
      '#prefix' => '<h3>',
      '#suffix' => '</h3></br>',
    ];


    $build['inspection_plans_edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => 'See all Inspection Plans',
      ]),
    ];

    $build['advice'] = [
      '#type' => 'fieldset',
      '#title' => t('Advice and Documents:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $build['advice']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => 'See all Advice'
      ]),
    ];



    // Contacts.
    // Local Authority.
    $par_data_contacts = $par_data_partnership->getAuthorityPeople();
    $build['authority_contact'] = [
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

        $build['authority_contact'][$person->id()] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $build['authority_contact'][$person->id()]['person'] = $this->renderMarkupField($alternative_person);
      }
    }
    else {
      $build['authority_contact']['details'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
      ];
    }

    // Primary contact summary.
    $par_data_contacts = $par_data_partnership->getOrganisationPeople();

    $build['organisation_contact'] = [
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

        $build['organisation_contact'][$person->id()] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $build['organisation_contact'][$person->id()]['person'] = $this->renderMarkupField($alternative_person);
      }
    }
    else {
      $build['organisation_contact']['details'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
      ];

    }

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);


    $this->addCacheableDependency($partnership_bundle);
    $this->addCacheableDependency($person_bundle);
    $this->addCacheableDependency($legal_entity_bundle);
    $this->addCacheableDependency($premises_bundle);

    return parent::build($build);

  }
}
