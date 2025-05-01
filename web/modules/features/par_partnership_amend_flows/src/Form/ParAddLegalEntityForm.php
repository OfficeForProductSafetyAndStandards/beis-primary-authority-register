<?php

namespace Drupal\par_partnership_amend_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The form for adding new legal entities.
 */
class ParAddLegalEntityForm extends ParBaseForm {

  protected $pageTitle = 'Add Legal Entities';

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData() {
    // There is no extra data to load at the moment.
    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

}
