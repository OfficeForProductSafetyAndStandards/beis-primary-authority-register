<?php

namespace Drupal\par_partnership_contact_update_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for displaying the application confirmation.
 */
class ParConfirmedController extends ParBaseController {

  protected $pageTitle = 'Thank you!';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {

    // Redirect to return page.
    return [];
  }

}
