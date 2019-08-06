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
 * The confirming the user is authorised to delete partnerships.
 */
class ParRdHelpDeskDeleteConfirmForm extends ParBaseForm {

  use ParDisplayTrait;

  /**
   * {@inheritdoc}
   */
  protected $flow = 'delete_partnership';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'Help Desk | Delete a partnership';
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

    // If partnership has been deleted, we should not be able to re-delete it.
    if ($par_data_partnership->isDeleted()) {
     $this->accessResult = AccessResult::forbidden('The partnership is deleted.');
    }

    // If partnership has been revoked, we should not be able to delete it.
    if ($par_data_partnership->isRevoked()) {
      $this->accessResult = AccessResult::forbidden('The partnership is revoked.');
    }

    // 403 if the partnership is in active it can't be deleted.
    if ($par_data_partnership->isActive()) {
      $this->accessResult = AccessResult::forbidden('The partnership is active.');
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

    if ($par_data_partnership && $par_data_partnership->isActive()) {
      $form['partnership_info'] = [
        '#type' => 'markup',
        '#title' => $this->t('Deletion denied'),
        '#markup' => $this->t('This partnership cannot be deleted because it is active. Please use the revoke process instead.'),
      ];

      return parent::buildForm($form, $form_state);
    }

    $form['partnership_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Delete the partnership'),
      '#attributes' => ['class' => 'form-group'],
    ];

    $form['partnership_info']['partnership_text'] = [
      '#type' => 'markup',
      '#markup' => $par_data_partnership->label(),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Enter the deletion reason.
    $form['deletion_reason'] = [
      '#title' => $this->t('Enter the reason you are deleting this partnership application'),
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

    // We only want to update the status of none active partnerships.
    if ($par_data_partnership->inProgress()) {
      $deleted = $par_data_partnership->delete($this->getFlowDataHandler()->getTempDataValue('deletion_reason'));

      if ($deleted) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('Deletion reason could not be saved for %form_id');
        $replacements = [
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }

    }
  }

}
