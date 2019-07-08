<?php

namespace Drupal\par_partnership_contact_add_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;

trait ParFormCancelTrait {

  /**
   * Cancel submit handler to clear all the current flow temporary form data.
   *
   * Because the route to redirect to on cancelation of all forms in this flow
   * is not in this flow we need to handle it outside of flow configuration.
   */
  public function cancelForm(array &$form, FormStateInterface $form_state) {
    // Delete form storage.
    $this->getFlowDataHandler()->deleteStore();

    // Go to cancel route.
    switch ($this->getFlowDataHandler()->getParameter('type')) {
      case 'organisation':
        $cancel_route = 'par_partnership_flows.organisation_details';

        break;

      case 'authority':
        $cancel_route = 'par_partnership_flows.authority_details';

        break;
    }

    if ($cancel_route) {
      $params = $this->getRouteParams();
      $form_state->setRedirect($cancel_route, [$params['par_data_partnership']]);
    }
  }
}
