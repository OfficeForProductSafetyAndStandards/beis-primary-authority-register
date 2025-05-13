<?php

namespace Drupal\par_person_membership_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;

/**
 * The form for selecting the institution to add.
 */
class ParSelectInstitutionForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Choose which institution to add';

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData() {
    parent::loadData();

    // Because this form is only to add memberships, do not pre-populate defaults.
    $this->getFlowDataHandler()->setFormPermValue('par_data_authority_id', []);
    $this->getFlowDataHandler()->setFormPermValue('par_data_organisation_id', []);
  }

}
