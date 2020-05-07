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
    return 'Confirmation | Choose the regulatory functions';
  }

  /**
   * {@inheritdoc}
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL) {
    try {
      // Get a new flow negotiator that points the route being checked for access.
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
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $selected_regulatory_functions = array_filter($this->getFlowDataHandler()->getTempDataValue('regulatory_functions'));

    // We only want to update the status of none active partnerships.
    if ($partnership->getRawStatus() !== 'confirmed_rd') {
      try {
        $partnership->setParStatus('confirmed_rd');
      }
      catch (ParDataException $e) {
        // If the partnership could not be saved the application can't be progressed.
        // @TODO Find a better way to alert the user without redirecting them away from the form.
        drupal_set_message('There was an error approving this partnership, please check it is ready to be approved.');
        $form_state->setRedirect($this->getFlowNegotiator()->getFlow()->progressRoute('cancel'));
        return;
      }

      $partnership->set('field_regulatory_function', $selected_regulatory_functions);

      // Set approved date to today.
      $time = new \DateTime();
      $partnership->set('approved_date', $time->format("Y-m-d"));

      if (!$partnership->save()) {

        $message = $this->t('This %partnership could not be approved for %form_id');
        $replacements = [
          '%partnership' => $partnership->id(),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }
  }

}
