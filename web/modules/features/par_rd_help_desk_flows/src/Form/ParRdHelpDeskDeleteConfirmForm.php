<?php

namespace Drupal\par_rd_help_desk_flows\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL): AccessResult {
    try {
      // Get a new flow negotiator that points the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException $ignore) {

    }

    // If the partnership is not deletable.
    if (!$par_data_partnership->isDeletable()) {
      $this->accessResult = AccessResult::forbidden('The partnership is not deletable.');
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

    $form['partnership_info'] = [
      '#type' => 'container',
      '#attributes' => ['class' => 'govuk-form-group'],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Delete the partnership'),
        '#attributes' => ['class' => ['govuk-heading-m']],
      ],
      'text' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $par_data_partnership->label(),
      ],
    ];

    // Enter the deletion reason.
    $form['deletion_reason'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Enter the reason you are deleting this partnership application'),
      '#title_tag' => 'h2',
      '#rows' => 5,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('revocation_reason', FALSE),
    ];

    // Change the primary action text.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Delete');

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if (!$par_data_partnership->isDeletable()) {
      $id = $this->getElementId('deletion_reason', $form);
      $form_state->setErrorByName($this->getElementName(['deletion_reason']), $this->wrapErrorMessage('This partnership cannot be deleted.', $id));
    }

    if (!$form_state->getValue('deletion_reason')) {
      $id = $this->getElementId('deletion_reason', $form);
      $form_state->setErrorByName($this->getElementName(['deletion_reason']), $this->wrapErrorMessage('Please supply the reason for cancelling this partnership.', $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Check the partnership can be deleted before progressing.
    if ($par_data_partnership->isDeletable()) {
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
