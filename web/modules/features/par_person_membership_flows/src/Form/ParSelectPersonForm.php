<?php

namespace Drupal\par_person_membership_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;

/**
 * The form for selecting the person to add.
 */
class ParSelectPersonForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Choose which person to add';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    parent::loadData();
  }

}
