<?php

namespace Drupal\par_member_list_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * Update the number of members.
 */
class ParUpdateMemberNumberForm extends ParBaseForm {

  protected $pageTitle = 'How many members are in this list?';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()
      ->getParameter('par_data_partnership');

    parent::loadData();
  }

}
