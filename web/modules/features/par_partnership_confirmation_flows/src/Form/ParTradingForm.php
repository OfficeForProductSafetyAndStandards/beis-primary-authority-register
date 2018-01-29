<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the trading name details.
 */
class ParTradingForm extends ParBaseForm {

  /**
   * Set the page title.
   */
  protected $pageTitle = "Confirm the trading name";

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_confirmation_trading_name';
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $partnership ? $partnership->getOrganisation(TRUE) : NULL;

    // For the apply journey we will always edit the first value.
    $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);
    $this->getFlowDataHandler()->setParameter('trading_name_delta', 0);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the trading name field.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $trading_name_delta = $this->getFlowDataHandler()->getParameter('trading_name_delta');

    $items = $par_data_organisation->get('trading_name')->getValue();

    if (!isset($trading_name_delta)) {
      $items[] = $this->getFlowDataHandler()->getTempDataValue('trading_name');
    }
    else {
      $items[$trading_name_delta] = $this->getFlowDataHandler()->getTempDataValue('trading_name');
    }

    $par_data_organisation->set('trading_name', $items);

    if ($par_data_organisation->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('This %field could not be saved for %form_id');
      $replacements = [
        '%field' => $this->getFlowDataHandler()->getTempDataValue('trading_name'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
