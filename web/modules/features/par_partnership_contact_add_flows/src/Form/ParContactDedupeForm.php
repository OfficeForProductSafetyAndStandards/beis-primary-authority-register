<?php

namespace Drupal\par_partnership_contact_add_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_contact_add_flows\ParFlowAccessTrait;

/**
 * The partnership contact dedupe form.
 */
class ParContactDedupeForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Choose an existing person';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    parent::loadData();
  }

}
