<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the trading name details.
 */
class ParPartnershipFlowsTradingForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_trading_name';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $trading_name_delta = $this->getRouteParam('trading_name_delta');

    $action = $this->t(is_numeric($trading_name_delta) ? 'Edit' : 'Add another');

    $this->pageTitle = "Update partnership information | {$action} trading name for your organisation";

    return $this->pageTitle;
  }

  /**
   * Helper to get all the editable values.
   *
   * Used for when editing or revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param int $trading_name_delta
   *   The trading name delta.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, $trading_name_delta = NULL) {

    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $bundle = $par_data_organisation->bundle();

    $this->formItems = [
      "par_data_organisation:{$bundle}" => [
        'trading_name' => 'trading_name',
      ],
    ];

    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }

    if (!is_null($trading_name_delta)) {
      // Store the current value of the sic_code if it's being edited.
      $trading_name = $par_data_organisation ? $par_data_organisation->get('trading_name')->getValue()[$trading_name_delta] : NULL;

      if ($trading_name) {
        $this->loadDataValue("trading_name", $trading_name);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, $trading_name_delta = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $trading_name_delta);

    $form['trading_name_fieldset'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#title' => $this->t('Enter a trading name'),
    ];

    $form['trading_name_fieldset']['trading_name'] = [
      '#type' => 'textfield',
      '#default_value' => $this->getDefaultValues("trading_name"),
      '#description' => $this->t("<p>Sometimes companies trade under a different name to their registered, legal name. This is known as a 'trading name'. State any trading names used by the organisation.</p>"),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the trading name field.
    $par_data_partnership = $this->getRouteParam('par_data_partnership');
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $trading_name_delta = $this->getRouteParam('trading_name_delta');

    $items = $par_data_organisation->get('trading_name')->getValue();

    if (!isset($trading_name_delta)) {
      $items[] = $this->getTempDataValue('trading_name');
    }
    else {
      $items[$trading_name_delta] = $this->getTempDataValue('trading_name');
    }

    $par_data_organisation->set('trading_name', $items);

    if ($par_data_organisation->save()) {
      $this->deleteStore();
    }
    else {
      $message = $this->t('This %field could not be saved for %form_id');
      $replacements = [
        '%field' => $this->getTempDataValue('trading_name'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
