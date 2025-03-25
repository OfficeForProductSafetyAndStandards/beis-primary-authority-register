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
  #[\Override]
  public function titleCallback() {
    return 'Confirmation | Revoke a partnership';
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL): AccessResult {
    try {
      // Get a new flow negotiator that points the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException) {

    }

    // If the partnership can't be revoked.
    if (!$par_data_partnership->isRevocable()) {
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
  #[\Override]
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
      '#type' => 'container',
      '#attributes' => ['class' => 'govuk-form-group'],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Revoke the partnership'),
        '#attributes' => ['class' => ['govuk-heading-m']],
      ],
      'text' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $par_data_partnership->label(),
      ],
    ];

    // Enter the revokcation reason.
    $form['revocation_reason'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Enter the reason you are revoking this partnership'),
      '#title_tag' => 'h2',
      '#rows' => 5,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('revocation_reason', FALSE),
    ];

    // Change the primary action text.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Revoke');

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Validate that the partnership is revocable.
    if (!$par_data_partnership?->isRevocable()) {
      $id = $this->getElementId('revocation_reason', $form);
      $form_state->setErrorByName($this->getElementName(['revocation_reason']), $this->wrapErrorMessage('This partnership cannot be revoked.', $id));
    }

    // Validate that a reason has been provided for revoking the partnership.
    if (empty($form_state->getValue('revocation_reason'))) {
      $id = $this->getElementId('revocation_reason', $form);
      $form_state->setErrorByName($this->getElementName(['revocation_reason']), $this->wrapErrorMessage('You must give a reason for revoking this partnership.', $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Check the partnership can be revoked before progressing.
    if ($par_data_partnership->isRevocable()) {
      $revoked = $par_data_partnership->revoke(TRUE, $this->getFlowDataHandler()->getTempDataValue('revocation_reason'));

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
