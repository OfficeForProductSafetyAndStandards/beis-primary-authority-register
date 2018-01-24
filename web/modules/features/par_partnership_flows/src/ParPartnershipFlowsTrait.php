<?php

namespace Drupal\par_partnership_flows;

use Drupal\par_flows\ParFlowException;
use Drupal\user\Entity\User;

/**
 * The base form controller for all PAR forms.
 *
 * This trait MUST be used only on forms on controllers that
 * extend the par_flows base form and controller.
 */

trait ParPartnershipFlowsTrait {

  /**
   * Get the current flow name.
   *
   * @return string
   *   The string representing the name of the current flow.
   */
  public function getFlowName() {
    // To proceed we need the current User account
    // and the partnership from the url.
    $account = User::Load($this->currentUser()->id());
    $par_data_partnership = $this->getRouteParam('par_data_partnership');

    // If the route is in only one flow then we're definately in that flow.
    $flows = \Drupal::entityTypeManager()->getStorage('par_flow')->loadByRoute($this->getCurrentRoute());
    if (count($flows) === 1) {
      return key($flows);
    }

    // If User has helpdesk permissions && the Route is in the helpdesk flow...
    if (isset($flows['helpdesk']) && $this->currentUser()->hasPermission('bypass par_data access')) {
      return 'helpdesk';
    }

    // IF Route is in authority flow && User is an authority member...
    if (isset($flows['partnership_authority']) && $par_data_partnership && $par_data_partnership->isAuthorityMember($account)) {
      return 'partnership_authority';
    }

    // If Route is in direct flow && partnership is direct...
    if (isset($flows['partnership_direct']) && $par_data_partnership && $par_data_partnership->isDirect()) {
      return 'partnership_direct';
    }

    // If Route is in coordinated flow && partnership is coordinated...
    if (isset($flows['partnership_coordinated']) && $par_data_partnership && $par_data_partnership->isCoordinated()) {
      return 'partnership_coordinated';
    }

    // If Route is in direct confirmation flow && partnership is direct...
    if (isset($flows['partnership_direct_application']) && $par_data_partnership && $par_data_partnership->isDirect()) {
      return 'partnership_direct_application';
    }

    // If Route is in coordinated flow && partnership is coordinated...
    if (isset($flows['partnership_coordinated_application']) && $par_data_partnership && $par_data_partnership->isCoordinated()) {
      return 'partnership_coordinated_application';
    }

    if (isset($flows['partnership_application'])) {
      return 'partnership_application';
    }

    // Throw an error if the flow is still ambiguous.
    if (empty($this->flow) && count($flows) >= 1) {
      throw new ParFlowException('The flow name is ambiguous.');
    }

    return parent::getFlowName();
  }

}
