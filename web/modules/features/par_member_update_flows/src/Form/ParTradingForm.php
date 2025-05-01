<?php

namespace Drupal\par_member_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_update_flows\ParFlowAccessTrait;

/**
 * Add trading names.
 */
class ParTradingForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Add trading name";

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataCoordinatedBusiness $par_data_coordinated_business = NULL, $trading_name_delta = NULL) {
    $this->getFlowDataHandler()->setParameter('trading_name_delta', $trading_name_delta);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData() {
    $par_data_coordinated_business = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business');
    $par_data_organisation = $par_data_coordinated_business->getOrganisation(TRUE);
    $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);
    parent::loadData();
  }

  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');
    $trading_name_delta = $this->getFlowDataHandler()->getParameter('trading_name_delta');

    $trading_name = $this->getFlowDataHandler()->getTempDataValue('trading_name');

    // If a delta is specified we should update this delta only.
    if (isset($trading_name_delta)) {
      try {
        $par_data_organisation->get('trading_name')->set((int) $trading_name_delta, $trading_name);
      } catch (MissingDataException $e) {
        $message = $this->t('Trading name could not be saved for %form_id due to missing data: %error');
        $replacements = [
          '%form_id' => $this->getFormId(),
          '%error' => $e->getMessage(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }
    // Otherwise append the new data to the trading name field.
    else {
      $par_data_organisation->get('trading_name')->appendItem($trading_name);
    }

    // Commit to organisation.
    if ($par_data_organisation->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('Trading names could not be saved for %form_id');
      $replacements = [
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }

  }

}
