<?php

namespace Drupal\par_rd_help_desk_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;

/**
 * The confirming the user is authorised to revoke partnerships.
 */
class ParRdHelpDeskUnrevokeConfirmForm extends ParBaseForm {

  use ParDisplayTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'Confirmation | Restore a partnership';
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

    // If partnership has been revoked, we should not be able to re-revoke it.
    if (!$par_data_partnership->isRevoked()) {
      $this->accessResult = AccessResult::forbidden('The partnership needs to be revoked to be restorable.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $form['partnership_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Restore the partnership'),
      '#attributes' => ['class' => 'form-group'],
    ];

    $form['partnership_info']['partnership_text'] = [
      '#type' => 'markup',
      '#markup' => $par_data_partnership->label(),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // We only want to update the status of active partnerships.
    if ($par_data_partnership->isRevoked()) {
      $restored = $par_data_partnership->unrevoke();

      if ($restored) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('Partnership restored: %partnership');
        $replacements = [
          '%partnership' => $par_data_partnership->label(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }

    }
  }

}
