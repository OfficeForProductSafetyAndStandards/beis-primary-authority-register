<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Enforcement review summary form plugin.
 *
 * @ParForm(
 *   id = "enforcement_review_summary",
 *   title = @Translation("Enforcement review summary.")
 * )
 */
class ParEnforcementReviewSummary extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    if ($enforcement_notice && $enforcing_officer = $enforcement_notice->getEnforcingPerson(TRUE)) {
      $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_name", $enforcing_officer->label());
      $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_work_phone", $enforcing_officer->get('work_phone')->getString());
      $this->getFlowDataHandler()->setFormPermValue("enforcing_officer_email", $enforcing_officer->get('email')->getString());
    }

    $enforcing_authority = $enforcement_notice->getEnforcingAuthority(TRUE);
    if ($enforcement_notice && $enforcing_authority = $enforcement_notice->getEnforcingAuthority(TRUE)) {
      $this->getFlowDataHandler()->setFormPermValue("enforcing_authority", $enforcing_authority->label());
    }

    if ($enforcement_notice && $enforced_organisation_name = $enforcement_notice->getEnforcedEntityName()) {
      $this->getFlowDataHandler()->setFormPermValue("enforced_organisation", $enforced_organisation_name);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    if ($enforcing_officer_name = $this->getDefaultValuesByKey('enforcing_officer_name', $cardinality, NULL)) {
      $form['enforcement_officer'] = [
        '#type' => 'fieldset',
        '#title' => t('Enforcement officer'),
        '#attributes' => ['class' => 'form-group'],
        'name' => [
          '#type' => 'markup',
          '#markup' => $enforcing_officer_name,
        ],
      ];
      if ($work_phone = $this->getDefaultValuesByKey('enforcing_officer_work_phone', $cardinality, NULL)) {
        $form['enforcement_officer']['work_phone'] = [
          '#type' => 'markup',
          '#markup' => $work_phone,
        ];
      }
      if ($email = $this->getDefaultValuesByKey('enforcing_officer_email', $cardinality, NULL)) {
        $form['enforcement_officer']['email'] = [
          '#type' => 'markup',
          '#markup' => $email,
        ];
      }
    }

    if ($enforcing_authority = $this->getDefaultValuesByKey('enforcing_authority', $cardinality, NULL)) {
      $form['enforced_authority'] = [
        '#type' => 'fieldset',
        '#title' => t('Enforcing authority'),
        '#attributes' => ['class' => 'form-group'],
        'enforcing_officer' => [
          '#type' => 'markup',
          '#markup' => $enforcing_authority,
        ]
      ];
    }

    if ($enforced_organisation = $this->getDefaultValuesByKey('enforced_organisation', $cardinality, NULL)) {
      $form['enforced_organisation'] = [
        '#type' => 'fieldset',
        '#title' => t('Enforced organisation'),
        '#attributes' => ['class' => 'form-group'],
        'enforcing_officer' => [
          '#type' => 'markup',
          '#markup' => $enforced_organisation,
        ]
      ];
    }

    return $form;
  }
}
