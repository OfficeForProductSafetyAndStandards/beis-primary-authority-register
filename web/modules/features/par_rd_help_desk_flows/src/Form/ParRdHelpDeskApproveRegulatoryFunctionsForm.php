<?php

namespace Drupal\par_rd_help_desk_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;

/**
 * The confirming the user is authorised to approve partnerships.
 */
class ParRdHelpDeskApproveRegulatoryFunctionsForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'approve_partnership';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'Confirmation | Is this a bespoke or sequenced partnership?';
  }

  /**
   * {@inheritdoc}
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL) {
    try {
      // Get a new flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException $e) {

    }

    // If partnership has been revoked, we should not be able to approve it.
    // @todo This needs to be re-addressed as per PAR-1082.
    if ($par_data_partnership->isRevoked()) {
      $this->accessResult = AccessResult::forbidden('The partnership has been revoked.');
    }

    // If partnership has been deleted, we should not be able to revoke it.
    if ($par_data_partnership->isDeleted()) {
      $this->accessResult = AccessResult::forbidden('The partnership is already deleted.');
    }

    // 403 if the partnership is active/approved by RD.
    if ($par_data_partnership->getRawStatus() !== 'confirmed_business') {
      $this->accessResult = AccessResult::forbidden('The partnership is active.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

}
