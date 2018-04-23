<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "enforcement_summary",
 *   title = @Translation("Enforcement summary.")
 * )
 */
class ParEnforcementSummary extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $enforcing_officer_cid = $this->getFormCid('enforcing_officer');
    if ($enforcing_officer_id = $this->getFlowDataHandler()->getTempDataValue('enforcement_officer_id', $enforcing_officer_cid)) {
      $enforcing_officer = ParDataPerson::load($enforcing_officer_id);
      if ($enforcing_officer) {
        $this->getFlowDataHandler()->setParameter('enforcing_officer', $enforcing_officer);
      }
    }

    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      // @TODO if we use this component on any referred enforcements this will need to change.
      $this->getFlowDataHandler()->setParameter('enforced_authority', $par_data_partnership->getAuthority(TRUE));
    }

    $enforced_organisation_cid = $this->getFormCid('organisation_selection');
    if ($enforced_organisation_id = $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', NULL, $enforced_organisation_cid)) {
      $enforced_organisation = ParDataOrganisation::load($enforced_organisation_id);
      if ($enforced_organisation) {
        $this->getFlowDataHandler()->setParameter('enforced_organisation', $enforced_organisation);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    if ($enforcing_officer = $this->getFlowDataHandler()->getParameter('enforcing_officer')) {
      $form['enforcement_officer'] = [
        '#type' => 'fieldset',
        '#title' => t('Enforcement officer'),
        '#attributes' => ['class' => 'form-group'],
        'enforcing_officer' => [
          '#type' => 'markup',
          '#markup' => $enforcing_officer->label(),
        ]
      ];
    }

    if ($enforced_authority = $this->getFlowDataHandler()->getParameter('enforced_authority')) {
      $form['enforced_authority'] = [
        '#type' => 'fieldset',
        '#title' => t('Enforced authority'),
        '#attributes' => ['class' => 'form-group'],
        'enforcing_officer' => [
          '#type' => 'markup',
          '#markup' => $enforced_authority->label(),
        ]
      ];
    }

    if ($enforced_organisation = $this->getFlowDataHandler()->getParameter('enforced_organisation')) {
      $form['enforced_organisation'] = [
        '#type' => 'fieldset',
        '#title' => t('Enforced organisation'),
        '#attributes' => ['class' => 'form-group'],
        'enforcing_officer' => [
          '#type' => 'markup',
          '#markup' => $enforced_organisation->label(),
        ]
      ];
    }

    return $form;
  }
}
