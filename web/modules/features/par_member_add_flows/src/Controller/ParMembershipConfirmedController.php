<?php

namespace Drupal\par_member_add_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_member_add_flows\ParPartnershipFlowsTrait;

/**
 * A controller for displaying the member confirmation.
 */
class ParMembershipConfirmedController extends ParBaseController {

  protected $pageTitle = 'Member added';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {

    // Display the help contact fo this partnership.
    $build['help_text'] = $this->renderSection('If you have any questions you can contact', $par_data_partnership, ['field_authority_person' => 'summary'], [], TRUE, TRUE);

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);
  }

}
