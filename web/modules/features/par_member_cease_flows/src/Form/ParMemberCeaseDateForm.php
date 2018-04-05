<?php

namespace Drupal\par_member_cease_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_cease_flows\ParFlowAccessTrait;

/**
 * Enter the date the membership began.
 */
class ParMemberCeaseDateForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  public function titleCallback(ParDataPartnership $par_data_partnership = NULL, ParDataCoordinatedBusiness $par_data_coordinated_business = NULL) {
    $member = $par_data_coordinated_business->getOrganisation(TRUE);
    $this->pageTitle = "Cease membership for {$member->label()}";

    return parent::titleCallback();
  }

}
