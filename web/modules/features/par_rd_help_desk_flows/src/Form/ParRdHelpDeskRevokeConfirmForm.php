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
class ParRdHelpDeskRevokeConfirmForm extends ParBaseForm {

  use ParDisplayTrait;

  /**
   * {@inheritdoc}
   */
  protected $flow = 'revoke_partnership';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'Confirmation | Revoke a partnership';
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
    if ($par_data_partnership->isRevoked()) {
      $this->accessResult = AccessResult::forbidden('The partnership is already revoked.');
    }

    // If partnership has been deleted, we should not be able to revoke it.
    if ($par_data_partnership->isDeleted()) {
       $this->accessResult = AccessResult::forbidden('The partnership is already deleted.');
    }

    // 403 if the partnership is in progress it can't be revoked.
    if ($par_data_partnership->inProgress()) {
      $this->accessResult = AccessResult::forbidden('The partnership is not active.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);

    if ($par_data_partnership && $par_data_partnership->inProgress()) {
      $form['partnership_info'] = [
        '#type' => 'markup',
        '#title' => $this->t('Revocation denied'),
        '#markup' => $this->t('This partnership cannot be revoked because it is awaiting approval or there are enforcement notices currently awaiting review. Please try again later.'),
      ];

      return parent::buildForm($form, $form_state);
    }

    $form['partnership_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Revoke the partnership'),
      '#attributes' => ['class' => 'form-group'],
    ];

    $form['partnership_info']['partnership_text'] = [
      '#type' => 'markup',
      '#markup' => $par_data_partnership->label(),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Enter the revokcation reason.
    $form['revocation_reason'] = [
      '#title' => $this->t('Enter the reason you are revoking this partnership'),
      '#type' => 'textarea',
      '#rows' => 5,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('revocation_reason', FALSE),
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
    if (!$par_data_partnership->isRevoked()) {
      $revoked = $par_data_partnership->revoke($this->getFlowDataHandler()->getTempDataValue('revocation_reason'));

      if ($revoked) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('Revocation reason could not be saved for %form_id');
        $replacements = [
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }

    }
  }

}
