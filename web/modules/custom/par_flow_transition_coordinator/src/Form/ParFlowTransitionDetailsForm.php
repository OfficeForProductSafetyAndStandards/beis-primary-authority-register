<?php

namespace Drupal\par_flow_transition_coordinator\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flow_transition_business\Form\ParFlowTransitionDetailsForm as ParFlowTransitionDetailsBusinessForm;

/**
 * The about partnership form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionDetailsForm extends ParFlowTransitionDetailsBusinessForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_coordinator';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_coordinator_details';
  }

  /**
   * Insert a value or key/value pair after a specific key in an array.  If key doesn't exist, value is appended
   * to the end of the array.
   *
   * @param array $array
   * @param string $key
   * @param array $new
   *
   * @return array
   */
  private function array_insert_after( array $array, $key, array $new ) {
    $keys = array_keys( $array );
    $index = array_search( $key, $keys );
    $pos = false === $index ? count( $array ) : $index + 1;
    return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {

    if ($par_data_partnership) {
      $this->loadDataValue("coordinator_suitable", $par_data_partnership->get('coordinator_suitable')->getString());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);
    $form = parent::buildForm($form, $form_state, $par_data_partnership);

    // Change labels for coordinator journey.
    $form['business_name']['#title'] = t('Association Name:');
    $form['about_business']['#title'] = t('About the association:');
    $form['primary_contact']['#title'] = t('Main association contact:');

    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $size = $par_data_organisation->get('size')->getString();
    $form_business['business_size'] = [
      '#type' => 'fieldset',
      '#title' => t('Number of associations:'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form_business['business_size']['number'] = [
      '#plain_text' => $size,
    ];

    $form_business['business_size']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getLinkByStep(12)->setText('edit')->toString(),
      ]),
    ];

    $form = $this->array_insert_after($form, 'about_business', $form_business);

    $form_confirm_suitable  ['suitable_nomination'] = [
      '#type' => 'checkbox',
      '#title' => t('I confirm the co-ordinator is suitable for nomination as a co-ordinating partner.'),
      '#default_value' => $this->getDefaultValues('coordinator_suitable'),
    ];
    $form = $this->array_insert_after($form, 'confirmation_section', $form_confirm_suitable);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save the value for the about_partnership field.
    $partnership = $this->getRouteParam('par_data_partnership');
    $this->setValue($partnership, 'coordinator_suitable', 'suitable_nomination');
    parent::submitForm($form, $form_state);
  }

}
