<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Enforcement summary form plugin.
 *
 * @ParForm(
 *   id = "enforcement_detail",
 *   title = @Translation("The full enforcement display.")
 * )
 */
class ParEnforcementDetail extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    if ($par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice')) {
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

      if ($par_data_organisation = $par_data_enforcement_notice->getEnforcedOrganisation(TRUE)) {
        $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);
      }

      if ($par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions()) {
        $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_actions);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    $form['enforcement_type'] = $this->renderSection('Type of enforcement notice', $par_data_enforcement_notice, ['notice_type' => 'full'], [], TRUE, TRUE);

    $form['enforcement_summary'] = $this->renderSection('Summary of enforcement notice', $par_data_enforcement_notice, ['summary' => 'summary'], [], TRUE, TRUE);

    // Display the details for each Enforcement Action.
    if ($par_data_enforcement_actions = $this->getFlowDataHandler()->getParameter('par_data_enforcement_actions')) {
      $form['enforcement_actions'] = $this->renderEntities('Enforcement Actions', $par_data_enforcement_actions, 'summary');
    }

    return $form;
  }
}
