<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the sic code details.
 */
class ParSicCodeForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_confirmation_sic_code';
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $partnership = $this->getflowDataHandler()->getParameter('par_data_partnership');
    $organisation = $partnership ? $partnership->getOrganisation(TRUE) : NULL;
    $par_data_sic = $organisation ? $organisation->getSicCode(TRUE) : NULL;

    // Override the route parameter so that data loaded will be from this entity.
    $this->getflowDataHandler()->setParameter('par_data_sic_code', $par_data_sic);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the edited value for the organisation's sic code field.
    $par_data_partnership = $this->getflowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $sic_code_delta = $this->getflowDataHandler()->getParameter('field_sic_code_delta');

    $items = $par_data_organisation->get('field_sic_code')->getValue();
    if ($par_data_organisation && isset($sic_code_delta)) {
      $items[$sic_code_delta] = $this->getFlowDataHandler()->getTempDataValue('sic_code');
    }
    else {
      $items[] = $this->getFlowDataHandler()->getTempDataValue('sic_code');
    }
    $par_data_organisation->set('field_sic_code', $items);

    if ($par_data_organisation->save()) {
      $this->getFlowDataHandler()->deleteStore();
    } else {
      $message = $this->t('This %field could not be saved for %form_id');
      $replacements = [
        '%field' => $this->getFlowDataHandler()->getTempDataValue('trading_name'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

  }

}
