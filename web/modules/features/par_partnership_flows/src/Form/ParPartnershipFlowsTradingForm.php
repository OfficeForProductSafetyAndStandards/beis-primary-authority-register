<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the trading name details.
 */
class ParPartnershipFlowsTradingForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $trading_name_delta = $this->getFlowDataHandler()->getParameter('trading_name_delta');

    // Check from the route if we are editing an existing trading name.
    $action = isset($trading_name_delta) ? 'Edit' : 'Add a';

    $this->pageTitle = "Update partnership information | {$action} trading name for your organisation";

    return $this->pageTitle;
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $trading_name_delta = $this->getFlowDataHandler()->getParameter('trading_name_delta');

    $par_data_organisation = $par_data_partnership?->getOrganisation(TRUE);

    if (!is_null($trading_name_delta)) {
      // Store the current value of the sic_code if it's being edited.
      $trading_name = $par_data_organisation ? $par_data_organisation->get('trading_name')->getValue()[$trading_name_delta] : NULL;

      if ($trading_name) {
        $this->getFlowDataHandler()->setFormPermValue("trading_name", $trading_name);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the trading name field.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);
    $trading_name_delta = $this->getFlowDataHandler()->getParameter('trading_name_delta');

    $items = $par_data_organisation->get('trading_name')->getValue();

    $trading_name = $this->getFlowDataHandler()->getTempDataValue('trading_name');

    if (!isset($trading_name_delta)) {
      $items[] = $trading_name;
    }
    else {
      $items[$trading_name_delta] = $trading_name;
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
