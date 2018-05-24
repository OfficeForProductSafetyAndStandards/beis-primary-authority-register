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
  public function loadData($cardinality = 1) {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    // If an enforcement notice parameter is set use this.
    if ($par_data_enforcement_notice) {
      if ($par_data_enforcement_notice && $enforcing_officer = $par_data_enforcement_notice->getEnforcingPerson(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_name", $enforcing_officer->label());
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_work_phone", $enforcing_officer->get('work_phone')->getString());
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_email", $enforcing_officer->get('email')->getString());
      }

      if ($par_data_enforcement_notice && $enforcing_authority = $par_data_enforcement_notice->getEnforcingAuthority(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("enforcing_authority", $enforcing_authority->label());
      }

      if ($par_data_enforcement_notice && $enforced_organisation_name = $par_data_enforcement_notice->getEnforcedEntityName()) {
        $this->getFlowDataHandler()->setFormPermValue("enforced_organisation", $enforced_organisation_name);
      }

      if ($par_data_enforcement_notice && $primary_authority = $par_data_enforcement_notice->getPrimaryAuthority(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("primary_authority", $primary_authority->label());

      }

      if ($par_data_enforcement_notice && $primary_authority_officer = $par_data_enforcement_notice->getPrimaryAuthorityContact()) {
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_name", $primary_authority_officer->label());
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_work_phone", $primary_authority_officer->get('work_phone')->getString());
        $this->getFlowDataHandler()->setFormPermValue("pa_officer_email", $primary_authority_officer->get('email')->getString());
      }
    }
    // Otherwise assume we're on the raise flow and use this data.
    else {
      $enforcing_officer_cid = $this->getFormCid('enforcing_officer');
      $enforcing_authority_cid = $this->getFormCid('authority_selection');
      $enforced_organisation_cid = $this->getFormCid('organisation_selection');
      $enforced_legal_entity_cid = $this->getFormCid('select_legal');

      // Set the enforcement officer details.
      if ($enforcing_officer_id = $this->getDefaultValuesByKey('enforcement_officer_id', $cardinality, NULL, $enforcing_officer_cid)) {
        if ($enforcing_officer = ParDataPerson::load($enforcing_officer_id)) {
          $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_name", $enforcing_officer->label());
          $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_work_phone", $enforcing_officer->get('work_phone')->getString());
          $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_email", $enforcing_officer->get('email')->getString());
        }
      }

      // Set the enforcing authority details.
      if ($enforcing_authority_id = $this->getDefaultValuesByKey('par_data_authority_id', $cardinality, NULL, $enforcing_authority_cid)) {
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

    parent::loadData($cardinality);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $enforcing_officer_name = $this->getDefaultValuesByKey('enforcing_officer_name', $cardinality, NULL);
    $enforcing_authority = $this->getDefaultValuesByKey('enforcing_authority', $cardinality, NULL);

    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    // Add the details about the enforcer.
    if ($enforcing_authority || $enforcing_officer_name) {
      $form['enforcer'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['grid-row', 'form-group']],
      ];

      if ($enforcing_officer_name) {
        $form['enforcer']['enforcement_officer'] = [
          '#type' => 'fieldset',
          '#title' => t('Enforcement officer'),
          '#attributes' => ['class' => 'column-one-half'],
          '#prefix' => '<p>',
          '#suffix' => '</p>',
          'name' => [
            '#type' => 'markup',
            '#markup' => $enforcing_officer_name,
          ],
        ];
        if ($work_phone = $this->getDefaultValuesByKey('enforcing_officer_work_phone', $cardinality, NULL)) {
          $form['enforcer']['enforcement_officer']['work_phone'] = [
            '#type' => 'markup',
            '#markup' => ', ' . $work_phone,
          ];
        }
        if ($email = $this->getDefaultValuesByKey('enforcing_officer_email', $cardinality, NULL)) {
          $form['enforcer']['enforcement_officer']['email'] = [
            '#type' => 'markup',
            '#markup' => ', ' . $email,
          ];
        }
      }

      if ($enforcing_authority) {
        $form['enforcer']['enforcing_authority'] = [
          '#type' => 'fieldset',
          '#title' => t('Enforcing authority'),
          '#attributes' => ['class' => 'column-one-half'],
          'enforcing_authority' => [
            '#type' => 'markup',
            '#markup' => $enforcing_authority,
            '#prefix' => '<p>',
            '#suffix' => '</p>',
          ]
        ];
      }
    }

    $primary_authority = $this->getDefaultValuesByKey('primary_authority', $cardinality, NULL);
    $enforced_organisation = $this->getDefaultValuesByKey('enforced_organisation', $cardinality, NULL);

    // Add the details about the partnership.
    if ($primary_authority || $enforced_organisation) {
      $form['partnership'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['grid-row', 'form-group']],
      ];

      if ($enforced_organisation) {
        $form['partnership']['enforced_organisation'] = [
          '#type' => 'fieldset',
          '#title' => t('Enforced organisation'),
          '#attributes' => ['class' => 'column-one-half'],
          'organisation_name' => [
            '#type' => 'markup',
            '#markup' => $enforced_organisation,
            '#prefix' => '<p>',
            '#suffix' => '</p>',
          ],
        ];
        try {
          $form['partnership']['enforced_organisation']['select_legal'] = [
            '#type' => 'markup',
            '#weight' => 99,
            '#markup' => t('@link', [
              '@link' => $this->getFlowNegotiator()->getFlow()
                ->getLinkByCurrentOperation('select_legal', $params, [])
                ->setText('Change the enforced organisation')
                ->toString(),
            ]),
          ];
        }
        catch (ParFlowException $e) {

        }
      }

      if ($primary_authority) {
        $form['partnership']['primary_authority'] = [
          '#type' => 'fieldset',
          '#title' => t('Primary authority'),
          '#attributes' => ['class' => 'column-one-half'],
          'primary_authority_name' => [
            '#type' => 'markup',
            '#markup' => $primary_authority,
            '#prefix' => '<p>',
            '#suffix' => '</p>',
          ],
        ];
      }
      if ($pa_officer_name = $this->getDefaultValuesByKey('pa_officer_name', $cardinality, NULL)) {
        $form['partnership']['primary_authority']['pa_officer'] = [
          '#type' => 'fieldset',
          '#prefix' => '<p>',
          '#suffix' => '</p>',
          'name' => [
            '#type' => 'markup',
            '#markup' => $pa_officer_name,
          ],
        ];
        if ($pa_officer_work_phone = $this->getDefaultValuesByKey('pa_officer_work_phone', $cardinality, NULL)) {
          $form['partnership']['primary_authority']['pa_officer']['work_phone'] = [
            '#type' => 'markup',
            '#markup' => ', ' . $pa_officer_work_phone,
          ];
        }
        if ($pa_officer_email = $this->getDefaultValuesByKey('pa_officer_email', $cardinality, NULL)) {
          $form['partnership']['primary_authority']['pa_officer']['email'] = [
            '#type' => 'markup',
            '#markup' => ', ' . $pa_officer_email,
          ];
        }
      }

      if ($enforcing_authority) {
        $form['enforcer']['enforcing_authority'] = [
          '#type' => 'fieldset',
          '#title' => t('Enforcing authority'),
          '#attributes' => ['class' => 'column-one-half'],
          'enforcing_authority' => [
            '#type' => 'markup',
            '#markup' => $enforcing_authority,
            '#prefix' => '<p>',
            '#suffix' => '</p>',
          ]
        ];
      }
    }

    return $form;
  }
}
