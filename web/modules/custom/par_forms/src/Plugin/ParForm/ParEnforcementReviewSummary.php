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
    $enforcing_officer_cid = $this->getFormCid('enforcing_officer');
    $enforced_organisation_cid = $this->getFormCid('organisation_selection');
    $enforced_legal_entity_cid = $this->getFormCid('select_legal');

    if ($enforcing_officer_id = $this->getFlowDataHandler()->getTempDataValue('enforcement_officer_id', $enforcing_officer_cid)) {
      $enforcing_officer = ParDataPerson::load($enforcing_officer_id);
      if ($enforcing_officer) {
        $this->getFlowDataHandler()->setFormPermValue("enforcing_officer", $enforcing_officer->label());
      }
    }

    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      // @TODO if we use this component on any referred enforcements this will need to change.
      $authority = $par_data_partnership->getAuthority(TRUE);
      $this->getFlowDataHandler()->setFormPermValue("enforced_authority", $authority->label());
    }

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

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    if ($enforcing_officer = $this->getDefaultValuesByKey('enforcing_officer', $cardinality, NULL)) {
      $form['enforcement_officer'] = [
        '#type' => 'fieldset',
        '#title' => t('Enforcement officer'),
        '#attributes' => ['class' => 'form-group'],
        'enforcing_officer' => [
          '#type' => 'markup',
          '#markup' => $enforcing_officer,
        ]
      ];
    }

    if ($enforced_authority = $this->getDefaultValuesByKey('enforced_authority', $cardinality, NULL)) {
      $form['enforced_authority'] = [
        '#type' => 'fieldset',
        '#title' => t('Enforced authority'),
        '#attributes' => ['class' => 'form-group'],
        'enforcing_officer' => [
          '#type' => 'markup',
          '#markup' => $enforced_authority,
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
