<?php

namespace Drupal\par_partnership_contact_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_contact_add_flows\ParFlowAccessTrait;
use Drupal\par_partnership_contact_add_flows\ParFormCancelTrait;

/**
 * The partnership contact dedupe form.
 */
class ParContactDedupeForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Choose an existing person';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    parent::loadData();
  }

}
