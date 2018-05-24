<?php

namespace Drupal\par_search_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for rendering the advice for a partnership.
 */
class ParPartnershipAdviceController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

}
