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
    $enforcing_officer_cid = $this->getFormDefaultByKey('enforcing_officer');
    if ($enforcing_officer_id = $this->getFlowDataHandler()->getTempDataValue('enforcement_officer_id', $enforcing_officer_cid)) {
      $enforcing_officer = ParDataPerson::load($enforcing_officer_id);
      if ($enforcing_officer) {
        $this->getFlowDataHandler()->setParameter('enforcing_officer', $enforcing_officer);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    $form['enforcement_officer'] = $this->renderSection('Enforcement officer', $this->getFlowDataHandler()->getParameter('enforcing_officer'), ['summary' => 'summary'], [], TRUE, TRUE);


    return $form;
  }
}
