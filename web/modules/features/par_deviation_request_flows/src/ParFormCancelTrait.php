<?php

namespace Drupal\par_deviation_request_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;

trait ParFormCancelTrait {

  protected $cancelRoute = 'par_search_partnership_flows.partnership_page';

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
    $form_state->setRedirect($this->cancelRoute, $this->getRouteParams());
  }
}
