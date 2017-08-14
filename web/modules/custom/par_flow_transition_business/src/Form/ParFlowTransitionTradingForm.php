<?php

namespace Drupal\par_flow_transition_business\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionTradingForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_business';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_business_trading';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
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
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, $trading_name_delta = NULL) {
    $this->retrieveEditableValues($par_data_partnership);
    $par_data_organisation = current($par_data_partnership->getOrganisation());

    if (empty($trading_name_delta)) {
      $form['intro'] = [
        '#markup' => $this->t('Add another trading name for your business'),
      ];

    }
    else {
      $form['intro'] = [
        '#markup' => $this->t('Change the trading name of your business'),
      ];
    }

    $form['trading_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of trading name'),
      '#default_value' => isset($par_data_organisation->get('trading_name')->getValue()[$trading_name_delta]) ? $par_data_organisation->get('trading_name')->getValue()[$trading_name_delta] : '',
      '#description' => $this->t('Sometimes companies trade under a different name to their registered, legal name. This is known as a \'trading name\'. State any trading names used by the business.'),
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
    ];

    $previous_link = $this->getFlow()->getLinkByStep(4)->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $previous_link]),
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
      $items[] =  $this->getTempDataValue('trading_name');
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

    // Go back to the overview.
    $form_state->setRedirect($this->getFlow()->getRouteByStep(4), $this->getRouteParams());
  }

}
