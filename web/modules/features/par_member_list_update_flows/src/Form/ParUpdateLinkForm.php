<?php

namespace Drupal\par_member_list_update_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;

/**
 * Update the link address.
 */
class ParUpdateLinkForm extends ParBaseForm {

  protected $pageTitle = 'Where is the list?';

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()
      ->getParameter('par_data_partnership');

    parent::loadData();
  }

}
