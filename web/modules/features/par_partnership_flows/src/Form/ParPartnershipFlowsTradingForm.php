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
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      $par_data_organisation = current($par_data_partnership->getOrganisation());
      $bundle = $par_data_organisation->bundle();

      $this->formItems = [
        "par_data_organisation:{$bundle}" => [
          'trading_name' => 'trading_name',
        ],
      ];

    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, $trading_name_delta = NULL) {
    $this->retrieveEditableValues($par_data_partnership);
    $par_data_organisation = current($par_data_partnership->getOrganisation());

    if (empty($trading_name_delta)) {
      $title = $this->t('Add another trading name for your organisation');
    }
    else {
      $title = $this->t('Edit the trading name of your organisation');
    }

    $form['intro'] = [
      '#markup' => $title,
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
    ];

    $form['trading_name'] = [
      '#type' => 'textfield',
      '#default_value' => isset($par_data_organisation->get('trading_name')->getValue()[$trading_name_delta]) ? $par_data_organisation->get('trading_name')->getValue()[$trading_name_delta] : '',
      '#description' => $this->t("Sometimes companies trade under a different name to their registered, legal name. This is known as a 'trading name'. State any trading names used by the organisation."),
    ];

    $form['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => t('Save'),
    ];

    $cancel_link = $this->getFlow()->getPrevLink('cancel')->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $cancel_link]),
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
