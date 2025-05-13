<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Enforcement summary form plugin.
 *
 * @ParForm(
 *   id = "enforcement_full_summary",
 *   title = @Translation("The enforcement summary when viewing a completed enforcement.")
 * )
 */
class ParEnforcementFullSummary extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
    $par_data_deviation_request = $this->getFlowDataHandler()->getParameter('par_data_deviation_request');
    $par_data_inspection_feedback = $this->getFlowDataHandler()->getParameter('par_data_inspection_feedback');
    $par_data_general_enquiry = $this->getFlowDataHandler()->getParameter('par_data_general_enquiry');

    // If an enforcement notice parameter is set use this.
    if ($par_data_enforcement_notice) {
      if ($enforcing_officer = $par_data_enforcement_notice->getEnforcingPerson(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_name", $enforcing_officer->label());
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_work_phone", $enforcing_officer->get('work_phone')->getString());
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_email", $enforcing_officer->get('email')->getString());
      }

      if ($enforcing_authority = $par_data_enforcement_notice->getEnforcingAuthority(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("enforcing_authority", $enforcing_authority->label());
      }

      if ($enforced_organisation_name = $par_data_enforcement_notice->getEnforcedEntityName()) {
        $this->getFlowDataHandler()->setFormPermValue("enforced_organisation", $enforced_organisation_name);
      }

      if ($primary_authority = $par_data_enforcement_notice->getPrimaryAuthority(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("primary_authority", $primary_authority->label());

      }

      if ($primary_authority_officer = $par_data_enforcement_notice->getPrimaryAuthorityContacts(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_name", $primary_authority_officer->label());
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_work_phone", $primary_authority_officer->get('work_phone')->getString());
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_email", $primary_authority_officer->get('email')->getString());
      }

      if ($referred_notice = $par_data_enforcement_notice->getReferringNotice()) {
        $referred_authority = $referred_notice->getPrimaryAuthority(TRUE);
        $this->getFlowDataHandler()->setFormPermValue("referred_authority", $referred_authority->label());
      }
    }
    // If a deviation request parameter is set use this.
    elseif ($par_data_deviation_request) {
      if ($enforcing_officer = $par_data_deviation_request->getEnforcingPerson(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_name", $enforcing_officer->label());
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_work_phone", $enforcing_officer->get('work_phone')->getString());
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_email", $enforcing_officer->get('email')->getString());
      }

      if ($enforcing_authority = $par_data_deviation_request->getEnforcingAuthority(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("enforcing_authority", $enforcing_authority->label());
      }

      if ($primary_authority = $par_data_deviation_request->getPrimaryAuthority(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("primary_authority", $primary_authority->label());

      }

      if ($primary_authority_officer = $par_data_deviation_request->getPrimaryAuthorityContacts(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_name", $primary_authority_officer->label());
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_work_phone", $primary_authority_officer->get('work_phone')->getString());
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_email", $primary_authority_officer->get('email')->getString());
      }
    }
    // If an inspeciton feedback request parameter is set use this.
    elseif ($par_data_inspection_feedback) {
      if ($enforcing_officer = $par_data_inspection_feedback->getEnforcingPerson(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_name", $enforcing_officer->label());
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_work_phone", $enforcing_officer->get('work_phone')->getString());
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_email", $enforcing_officer->get('email')->getString());
      }

      if ($enforcing_authority = $par_data_inspection_feedback->getEnforcingAuthority(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("enforcing_authority", $enforcing_authority->label());
      }

      if ($primary_authority = $par_data_inspection_feedback->getPrimaryAuthority(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("primary_authority", $primary_authority->label());

      }

      if ($primary_authority_officer = $par_data_inspection_feedback->getPrimaryAuthorityContacts(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_name", $primary_authority_officer->label());
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_work_phone", $primary_authority_officer->get('work_phone')->getString());
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_email", $primary_authority_officer->get('email')->getString());
      }
    }
    // If a general enquiry request parameter is set use this.
    elseif ($par_data_general_enquiry) {
      if ($enforcing_officer = $par_data_general_enquiry->getEnforcingPerson(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_name", $enforcing_officer->label());
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_work_phone", $enforcing_officer->get('work_phone')->getString());
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_email", $enforcing_officer->get('email')->getString());
      }

      if ($enforcing_authority = $par_data_general_enquiry->getEnforcingAuthority(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("enforcing_authority", $enforcing_authority->label());
      }

      if ($primary_authority = $par_data_general_enquiry->getPrimaryAuthority(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("primary_authority", $primary_authority->label());

      }

      if ($primary_authority_officer = $par_data_general_enquiry->getPrimaryAuthorityContacts(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_name", $primary_authority_officer->label());
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_work_phone", $primary_authority_officer->get('work_phone')->getString());
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_email", $primary_authority_officer->get('email')->getString());
      }
    }
    // Otherwise assume we're on the raise flow and use this data.
    else {
      $enforcing_officer_cid = $this->getFlowNegotiator()->getFormKey('enforcing_officer');
      $enforcing_authority_cid = $this->getFlowNegotiator()->getFormKey('authority_selection');
      $enforced_organisation_cid = $this->getFlowNegotiator()->getFormKey('organisation_selection');
      $enforced_legal_entity_cid = $this->getFlowNegotiator()->getFormKey('select_legal');

      // Set the enforcement officer details.
      if ($enforcing_officer_id = $this->getDefaultValuesByKey('enforcement_officer_id', $index, NULL, $enforcing_officer_cid)) {
        if ($enforcing_officer = ParDataPerson::load($enforcing_officer_id)) {
          $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_name", $enforcing_officer->label());
          $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_work_phone", $enforcing_officer->get('work_phone')->getString());
          $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_email", $enforcing_officer->get('email')->getString());
        }
      }

      // Set the enforcing authority details.
      if ($enforcing_authority_id = $this->getDefaultValuesByKey('par_data_authority_id', $index, NULL, $enforcing_authority_cid)) {
        if ($enforcing_authority = ParDataAuthority::load($enforcing_authority_id)) {
          $this->getFlowDataHandler()->setFormPermValue("enforcing_authority", $enforcing_authority->label());
        }
      }

      // Set the organisation details.
      $enforced_legal_entity_id = $this->getFlowDataHandler()->getDefaultValues('legal_entities_select', NULL, $enforced_legal_entity_cid);
      $enforced_organisation_id = $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', NULL, $enforced_organisation_cid);
      $alternate_legal_entity = $this->getFlowDataHandler()->getDefaultValues('alternative_legal_entity', NULL, $enforced_legal_entity_cid);
      // Get the alternate legal entity if there is one.
      if ($enforced_legal_entity_id && $enforced_legal_entity_id === ParSelectEnforcedLegalEntityForm::ADD_NEW) {
        $this->getFlowDataHandler()->setFormPermValue("enforced_organisation", $alternate_legal_entity);
      }
      // Or get the legal entity chosen if there is one.
      elseif ($enforced_legal_entity_id && $enforced_legal_entity = ParDataLegalEntity::load($enforced_legal_entity_id)) {
        $this->getFlowDataHandler()->setFormPermValue("enforced_organisation", $enforced_legal_entity->label());
      }
      // Or get the organisation selected if there is one.
      elseif ($enforced_organisation_id && $enforced_organisation = ParDataOrganisation::load($enforced_organisation_id)) {
        $this->getFlowDataHandler()->setFormPermValue("enforced_organisation", $enforced_organisation->label());
      }

      // Set the authority details.
      if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
        if ($primary_authority = $par_data_partnership->getAuthority(TRUE)) {
          $this->getFlowDataHandler()->setFormPermValue("primary_authority", $primary_authority->label());
        }
        if ($primary_authority_officer = $par_data_partnership->getAuthorityPeople(TRUE)) {
          $this->getFlowDataHandler()->setFormPermValue("pa_officer_name", $primary_authority_officer->label());
          $this->getFlowDataHandler()->setFormPermValue("pa_officer_work_phone", $primary_authority_officer->get('work_phone')->getString());
          $this->getFlowDataHandler()->setFormPermValue("pa_officer_email", $primary_authority_officer->get('email')->getString());
        }
      }
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    $enforcing_officer_name = $this->getDefaultValuesByKey('enforcing_officer_name', $index, NULL);
    $enforcing_authority = $this->getDefaultValuesByKey('enforcing_authority', $index, NULL);

    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    // Add the details about the enforcer.
    if ($enforcing_authority || $enforcing_officer_name) {
      $form['enforcer'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-grid-row', 'govuk-form-group', 'enforcement-officer']],
      ];

      if ($enforcing_officer_name) {
        $form['enforcer']['enforcement_officer'] = [
          '#type' => 'container',
          'heading' => [
            '#type' => 'html_tag',
            '#tag' => 'h2',
            '#attributes' => ['class' => ['govuk-heading-m']],
            '#value' => t('Enforcement officer'),
          ],
          '#attributes' => ['class' => 'govuk-grid-column-one-half'],
          'name' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $enforcing_officer_name,
          ],
        ];
        $enforcement_contact_details = array_filter([
          'phone' => $this->getDefaultValuesByKey('enforcing_officer_work_phone', $index, NULL),
          'email' => $this->getDefaultValuesByKey('enforcing_officer_email', $index, NULL),
        ]);
        if (!empty($enforcement_contact_details)) {
          $form['enforcer']['enforcement_officer']['contact_details'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => implode(', ', array_filter($enforcement_contact_details)),
          ];
        }
      }

      if ($enforcing_authority) {
        $form['enforcer']['enforcing_authority'] = [
          '#type' => 'container',
          'heading' => [
            '#type' => 'html_tag',
            '#tag' => 'h2',
            '#attributes' => ['class' => ['govuk-heading-m']],
            '#value' => t('Enforcing authority'),
          ],
          '#attributes' => ['class' => 'govuk-grid-column-one-half'],
          'enforcing_authority' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $enforcing_authority,
          ],
        ];
      }
    }

    $primary_authority = $this->getDefaultValuesByKey('primary_authority', $index, NULL);
    $enforced_organisation = $this->getDefaultValuesByKey('enforced_organisation', $index, NULL);
    $referring_authority = $this->getDefaultValuesByKey('referred_authority', $index, NULL);

    // Add the details about the partnership.
    if ($primary_authority || $enforced_organisation) {
      $form['partnership'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-grid-row', 'govuk-form-group']],
      ];

      if ($enforced_organisation) {
        $form['partnership']['enforced_organisation'] = [
          '#type' => 'container',
          'heading' => [
            '#type' => 'html_tag',
            '#tag' => 'h2',
            '#attributes' => ['class' => ['govuk-heading-m']],
            '#value' => t('Organisation'),
          ],
          '#attributes' => ['class' => 'govuk-grid-column-one-half'],
          'organisation_name' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $enforced_organisation,
          ],
        ];
        try {
          $link = $this->getFlowNegotiator()->getFlow()
            ->getOperationLink('select_legal', 'Change the enforced organisation', $params);
          $form['partnership']['enforced_organisation']['select_legal'] = [
            '#type' => 'markup',
            '#weight' => 99,
            '#markup' => t('@link', [
              '@link' => $link ? $link->toString() : '',
            ]),
          ];
        }
        catch (ParFlowException) {

        }
      }

      if ($primary_authority) {
        $form['partnership']['primary_authority'] = [
          '#type' => 'container',
          'heading' => [
            '#type' => 'html_tag',
            '#tag' => 'h2',
            '#attributes' => ['class' => ['govuk-heading-m']],
            '#value' => t('Primary authority'),
          ],
          '#attributes' => ['class' => ['govuk-grid-column-one-half', 'authority-officer']],
          'primary_authority_name' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $primary_authority,
          ],
        ];
      }
      if ($pa_officer_name = $this->getDefaultValuesByKey('pa_officer_name', $index, NULL)) {
        $form['partnership']['primary_authority']['pa_officer'] = [
          '#type' => 'container',
          'name' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $pa_officer_name,
          ],
        ];
        $pa_officer_contact_details = array_filter([
          'phone' => $this->getDefaultValuesByKey('pa_officer_work_phone', $index, NULL),
          'email' => $this->getDefaultValuesByKey('pa_officer_email', $index, NULL),
        ]);
        if (!empty($pa_officer_contact_details)) {
          $form['partnership']['primary_authority']['pa_officer']['contact_details'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => implode(', ', array_filter($pa_officer_contact_details)),
          ];
        }
      }

      if ($referring_authority) {
        $form['partnership']['referring_authority'] = [
          '#type' => 'container',
          'heading' => [
            '#type' => 'html_tag',
            '#tag' => 'h2',
            '#attributes' => ['class' => ['govuk-heading-m']],
            '#value' => t('Referred by'),
          ],
          '#attributes' => ['class' => 'govuk-grid-column-one-half'],
          'referring_authority_name' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $referring_authority,
          ],
        ];
      }

      if ($enforcing_authority) {
        $form['enforcer']['enforcing_authority'] = [
          '#type' => 'container',
          'heading' => [
            '#type' => 'html_tag',
            '#tag' => 'h2',
            '#attributes' => ['class' => ['govuk-heading-m']],
            '#value' => t('Enforcing authority'),
          ],
          '#attributes' => ['class' => 'govuk-grid-column-one-half'],
          'enforcing_authority' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $enforcing_authority,
          ],
        ];
      }
    }

    return $form;
  }

}
