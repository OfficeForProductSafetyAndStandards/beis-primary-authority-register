<?php

namespace Drupal\par_search_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\ParFlowException;

/**
 * A controller for rendering a specific partner page.
 */
class ParPartnershipPageController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    if ($par_data_partnership) {
      $par_data_organisation = current($par_data_partnership->getOrganisation());

      if ($par_data_organisation && $org_name = $par_data_organisation->get('organisation_name')->getString()) {
        $this->pageTitle = "Primary authority information for | {$org_name}";
      }
    }
    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function build($build = [], ParDataPartnership $par_data_partnership = NULL) {
    // Configuration for each entity is contained within the bundle.
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    $par_data_authority = $par_data_partnership->getAuthority(TRUE);
    $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);

    // In case of incorrect information.
    if (empty($par_data_authority) || empty($par_data_organisation)) {
      return [
        'invalid_partnership' => [
          '#type' => 'markup',
          '#markup' => 'There has been an error display this partnership, please contact the helpdesk if this error persists.',
          '#prefix' => '<h1>',
          '#suffix' => '</h1>',
        ]
      ];
    }


    // Partnership Authority Name - component.
    $build['authority_name'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "<span class='heading-secondary'>In partnership with</span>" . $par_data_authority->getName(),
      '#attributes' => ['class' => ['heading-large', 'form-group', 'authority-name']],
    ];


    // Partnership Basic Information - component.
    $build['partnership_info'] = [
      '#type' => 'container',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    // Display details about the partnership for information.
    $about_partnership_display = $par_data_partnership->about_partnership->view(['label' => 'hidden']);
    $build['partnership_info']['about_partnership'] = [
      '#type' => 'fieldset',
      '#title' => 'About the partnership',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'details' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $this->getRenderer()->render($about_partnership_display),
      ],
    ];

    // Display the regulatory functions and partnership approved date.
    $approved_date_display = $par_data_partnership->approved_date->view('full');
    $regulatory_functions = $par_data_partnership->get('field_regulatory_function')->referencedEntities();
    $build['partnership_info']['details'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['grid-row']],
      'regulatory_functions' => [
        '#type' => 'fieldset',
        '#title' => 'Partnered for',
        '#attributes' => ['class' => 'column-one-half'],
        'value' => [
          '#theme' => 'item_list',
          '#list_type' => 'ul',
          '#items' => $this->getParDataManager()->getEntitiesAsOptions($regulatory_functions),
        ]
      ],
      'approved_date' => [
        '#type' => 'fieldset',
        '#title' => 'In partnership since',
        '#attributes' => ['class' => 'column-one-half'],
        'value' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $this->getRenderer()->render($approved_date_display),
        ],
      ],
    ];


    // Get the link for the first step of the raise enforcement journey.
    try {
      $enforcement_notice_link = $this->getFlowNegotiator()->getFlow('raise_enforcement')->getLinkByStep(1, [], [], TRUE);
    } catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    $message_links['enforcement_notice_link'] = ['#type' => 'markup',
      '#markup' => $enforcement_notice_link ? $enforcement_notice_link->setText('Send a notification of a proposed enforcement action')->toString() : '<p>(none)</p>',
    ];

    // Get the link for the first step of the deviation request journey.
    try {
      $deviation_request_link = $this->getFlowNegotiator()->getFlow('deviation_request')->getLinkByStep(1, [], [], TRUE);
    } catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    $message_links['deviation_request_link'] = ['#type' => 'markup',
      '#markup' => $deviation_request_link ? $deviation_request_link->setText('Request to deviate from the inspection plan')->toString() : '<p>(none)</p>',
    ];

    // Get the link for the first step of the inspection plan feedback journey.
    try {
      $inspection_feedback_link = $this->getFlowNegotiator()->getFlow('inspection_feedback')->getLinkByStep(1, [], [], TRUE);
    } catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    $message_links['inspection_feedback_link'] = ['#type' => 'markup',
      '#markup' => $inspection_feedback_link ? $inspection_feedback_link->setText('Submit feedback following an inspection')->toString() : '<p>(none)</p>',
    ];

    // Get the link for the first step of the general enquiry journey.
    try {
      $general_enquiry_link = $this->getFlowNegotiator()->getFlow('enquiry')->getLinkByStep(1, [], [], TRUE);
    } catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    $message_links['general_enquiry_link'] = ['#type' => 'markup',
      '#markup' => $general_enquiry_link ? $general_enquiry_link->setText('Send a general enquiry to the primary authority')->toString() : '<p>(none)</p>',
    ];

    // Create a list of links for the actions that can be performed on this partnership.
    $build['partnership_actions'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#title' => t('Send a message about this organisation'),
      '#items' => $message_links,
      '#attributes' => ['class' => ['list', 'form-group']],
    ];


    // Partnership Organisation Information - component.
    $build['organisation_info'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "Information about the organisation",
      '#attributes' => ['class' => 'heading-large'],
    ];
    // Display the primary address along with the link to edit it.
    $build['registered_address'] = $this->renderSection('Address', $par_data_organisation, ['field_premises' => 'summary'], [], TRUE, TRUE);

    // View and perform operations on the information about the business.
    $build['about_business'] = $this->renderSection('About the organisation', $par_data_organisation, ['comments' => 'about']);


    // Only show SIC Codes and Employee number if the partnership is a direct partnership.
    if ($par_data_partnership->isDirect()) {
      // Add the SIC Codes with the relevant operational links.
      $build['sic_codes'] = $this->renderSection('SIC Codes', $par_data_organisation, ['field_sic_code' => 'full']);

      // Add the number of employees with a link to edit the field.
      $build['employee_no'] = $this->renderSection('Number of Employees', $par_data_organisation, ['employees_band' => 'full']);
    }

    // Only show Members list, Sectors and Number of businesses if the partnership is a coordinated partnership.
    if ($par_data_partnership->isCoordinated()) {
      $build['associations'] = $this->renderSection('Number of Members', $par_data_organisation, ['size' => 'full']);

      // Display all the legal entities along with the links for the allowed operations on these.
      $build['members'] = $this->renderSection('Members', $par_data_partnership, ['field_coordinated_business' => 'title']);
    }

    // Display all the legal entities along with the links for the allowed operations on these.
    $build['legal_entities'] = $this->renderSection('Legal Entities', $par_data_partnership, ['field_legal_entity' => 'summary']);

    // Display all the trading names along with the links for the allowed operations on these.
    $build['trading_names'] = $this->renderSection('Trading Names', $par_data_organisation, ['trading_name' => 'full']);


    // Partnership Documents - component.
    $build['documents'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "Documents",
      '#attributes' => ['class' => 'heading-large'],
    ];
    $build['inspection_plans'] = [
      '#type' => 'fieldset',
      '#title' => t('Inspection plans:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $build['inspection_plans']['link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()->getNextLink('inspection_plans')->setText('See all Inspection Plans')->toString(),
      ]),
    ];

    $build['advice'] = [
      '#type' => 'fieldset',
      '#title' => t('Advice and Documents:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $build['advice']['link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()->getNextLink('advice')->setText('See all Advice')->toString(),
      ]),
    ];


    // Partnership Contacts - component.
    $build['Contacts'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "Contact information",
      '#attributes' => ['class' => 'heading-large'],
    ];
    // Display the authority contacts for information.
    $build['authority_contacts'] = $this->renderSection('Primary Authority', $par_data_partnership, ['field_authority_person' => 'detailed'], ['edit-entity', 'add']);

    return parent::build($build);

  }
}
