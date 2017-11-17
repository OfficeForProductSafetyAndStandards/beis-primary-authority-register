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
  protected $flow = 'raise_enforcement';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $par_data_partnership = $this->getRouteParam('par_data_partnership');
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
  public function content(ParDataPartnership $par_data_partnership = NULL) {
    // Configuration for each entity is contained within the bundle.
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    $par_data_authority = current($par_data_partnership->getAuthority());
    $par_data_organisation = current($par_data_partnership->getOrganisation());

    // Display the primary address along with the link to edit it.
    $build['registered_address'] = $this->renderSection('Registered address', $par_data_organisation, ['field_premises' => 'summary'], [], FALSE, TRUE);

    // View and perform operations on the information about the business.
    $build['about_business'] = $this->renderSection('About the business', $par_data_organisation, ['comments' => 'about']);

    // Create links for the actions that can be performed on this partnership.
    $build['partnership_actions'] = [
      '#type' => 'markup',
      '#markup' => t('Enforcement Actions:'),
      '#attributes' => ['class' => ['form-group']],
    ];
    $build['partnership_actions']['link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('raise')->setText('Send notification of enforcement action')->toString(),
      ]),
    ];

    // Only show SIC Codes and Employee number if the partnership is a direct partnership.
    if ($par_data_partnership->isDirect()) {
      // Add the SIC Codes with the relevant operational links.
      $build['sic_codes'] = $this->renderSection('SIC Codes', $par_data_organisation, ['field_sic_code' => 'full']);

      // Add the number of employees with a link to edit the field.
      $build['employee_no'] = $this->renderSection('Number of Employees', $par_data_organisation, ['employees_band' => 'full']);
    }

    // Only show Members list, Sectors and Number of businesses if the partnership is a coordinated partnership.
    if ($par_data_partnership->isCoordinated()) {
      $build['associations'] = $this->renderSection('Number of Associations', $par_data_organisation, ['size' => 'full']);

      // Display all the legal entities along with the links for the allowed operations on these.
      $build['members'] = $this->renderSection('Members', $par_data_partnership, ['field_coordinated_business' => 'title']);
    }

    // Display all the legal entities along with the links for the allowed operations on these.
    $build['legal_entities'] = $this->renderSection('Legal Entities', $par_data_organisation, ['field_legal_entity' => 'summary']);

    // Display all the trading names along with the links for the allowed operations on these.
    $build['trading_names'] = $this->renderSection('Trading Names', $par_data_organisation, ['trading_name' => 'full']);

    // Everything below is for the authorioty to edit and add to.
    $build['authority'] = [
      '#type' => 'markup',
      '#markup' => $par_data_authority->get('authority_name')->getString(),
      '#prefix' => '<h1>',
      '#suffix' => '</h1>',
    ];

    // Display details about the partnership for information.
    $build['partnership_since'] = $this->renderSection('In partnership since', $par_data_partnership, ['approved_date' => 'full']);

    // Display details about the partnership for information.
    $build['regulatory_functions'] = $this->renderSection('Partnered for', $par_data_partnership, ['field_regulatory_function' => 'full']);

    // Display details about the partnership for information.
    $build['about_partnership'] = $this->renderSection('About the partnership', $par_data_partnership, ['about_partnership' => 'about'], ['edit-field']);

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
        '@link' => $this->getFlow()->getNextLink('inspection_plans')->setText('See all Inspection Plans')->toString(),
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
        '@link' => $this->getFlow()->getNextLink('advice')->setText('See all Advice')->toString(),
      ]),
    ];

    // Display the authority contacts for information.
    $build['authority_contacts'] = $this->renderSection('Contacts at the Primary Authority', $par_data_partnership, ['field_authority_person' => 'detailed'], ['edit-entity', 'add']);

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($partnership_bundle);
    $this->addCacheableDependency($person_bundle);
    $this->addCacheableDependency($legal_entity_bundle);
    $this->addCacheableDependency($premises_bundle);

    return parent::build($build);

  }
}
