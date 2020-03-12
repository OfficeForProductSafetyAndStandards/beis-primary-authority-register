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
class ParRdHelpDeskApproveAuthorisationForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'approve_partnership';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'Confirmation | Are you authorised to approve this partnership?';
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

    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $par_data_authority = current($par_data_partnership->getAuthority());

    $this->retrieveEditableValues($par_data_partnership);

    // Present partnership info.
    $form['partnership_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Partnership between'),
      '#attributes' => ['class' => 'form-group'],
    ];

    $form['partnership_info']['partnership_text'] = [
      '#type' => 'markup',
      '#markup' => $par_data_partnership->label(),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Auth.
    $form['partnership_approve'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Please confirm you are authorised to approve this partnership'),
      '#attributes' => ['class' => 'form-group'],
    ];

    $form['partnership_approve']['confirm_authorisation_select'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Yes, I am authorised to approve this partnership'),
      '#required' => TRUE,
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
}
